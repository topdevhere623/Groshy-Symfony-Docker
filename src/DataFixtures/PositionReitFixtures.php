<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Enum\TransactionType;
use Groshy\Message\Command\CreateTransactionCommand;
use Groshy\Message\Dto\Transaction\CreateTransactionDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;

final class PositionReitFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public const REIT_TYPE = 'Public Non Traded REIT';

    public function __construct(
        private readonly UserManagerInterface $userManager,
        private readonly ManagerInterface $assetTypeManager,
        private readonly ManagerInterface $assetManager,
        private readonly ManagerInterface $positionManager,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function loadData(): void
    {
        $type = $this->assetTypeManager->getRepository()->findOneBy(['name' => self::REIT_TYPE]);
        $reits = $this->assetManager->getRepository()->findBy(['assetType' => $type]);
        foreach ($reits as $reit) {
            $positions = $this->positionManager->getRepository()->findBy(['asset' => $reit]);
            foreach ($positions as $position) {
                $value = $position->getLastValue()->getValue();
                $period = new DatePeriod(
                    $position->getStartDate(),
                    new DateInterval('P1M'),
                    new DateTime(),
                    DatePeriod::EXCLUDE_START_DATE
                );
                foreach ($period as $date) {
                    // dividend transaction
                    $dividendValue = intval($value * $this->faker->numberBetween(400, 600) / 100 / 100 / 12);
                    $dto = new CreateTransactionDto();
                    $dto->position = $position;
                    $dto->valueDate = $date;
                    $dto->value = $dividendValue;
                    $dto->type = TransactionType::DISTRIBUTION;
                    $dto->isReinvested = $this->getDividendFlag($position->getCreatedBy());
                    $this->messageBus->dispatch(new CreateTransactionCommand($dto));

                    // share increase transaction
                    $value = intval($value + $value * $this->faker->numberBetween(-50, 150) / 10000);
                    $dto = new CreateTransactionDto();
                    $dto->position = $position;
                    $dto->valueDate = $date;
                    $dto->value = $value;
                    $dto->type = TransactionType::VALUE_UPDATE;
                    $this->messageBus->dispatch(new CreateTransactionCommand($dto));
                }
            }
        }
        $this->positionManager->flush();
    }

    public function getOrder(): int
    {
        return 30;
    }

    private function getDividendFlag($user): bool
    {
        if ('user1' == $user->getUsername()) {
            return true;
        }
        if ('user2' == $user->getUsername()) {
            return false;
        }

        return $this->faker->boolean;
    }
}
