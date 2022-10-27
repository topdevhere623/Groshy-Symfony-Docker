<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Entity\PositionCreditCard;
use Groshy\Entity\PositionCreditCardData;
use Groshy\Enum\TransactionType;
use Groshy\Message\Command\CreateTransactionCommand;
use Groshy\Message\Dto\Transaction\CreateTransactionDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;

final class PositionLiabilitiesFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly UserManagerInterface $userManager,
        private readonly ManagerInterface $positionCreditCardManager,
        private readonly ManagerInterface $assetCreditCardManager,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function loadData(): void
    {
        $cc = $this->assetCreditCardManager->getRepository()->findOneBy(['name' => 'Credit Card']);
        foreach ($this->userManager->getRepository()->findAll() as $user) {
            if ('user0' == $user->getUserName() || 'user4' != $user->getUserName() && $this->faker->numberBetween(1, 20) > 10) {
                continue;
            }

            /** @var PositionCreditCard $pos */
            $pos = $this->positionCreditCardManager->create();
            $data = new PositionCreditCardData();
            $data->setCardLimit($this->faker->numberBetween(10, 100) * 1000);
            $pos->setData($data);
            $pos->setCreatedBy($user);
            $pos->setAsset($cc);
            $this->positionCreditCardManager->update($pos);

            $dto = new CreateTransactionDto();
            $dto->position = $pos;
            $dto->valueDate = $this->faker->dateTimeBetween('-5 years', '-2 months');
            if ('user5' == $user->getUserName()) {
                $dto->value = $this->faker->numberBetween(100, 500) * 1000 * 100;
            } else {
                $dto->value = $this->faker->numberBetween(1, 10) * 1000 * 100;
            }
            $dto->type = TransactionType::BALANCE_UPDATE;
            $this->messageBus->dispatch(new CreateTransactionCommand($dto));
        }

        $this->positionCreditCardManager->flush();

        $positions = $this->positionCreditCardManager->getRepository()->findAll();
        foreach ($positions as $position) {
            $value = $position->getLastValue()->getValue();
            $period = new DatePeriod(
                $position->getStartDate(),
                new DateInterval('P1M'),
                new DateTime(),
                DatePeriod::EXCLUDE_START_DATE
            );
            foreach ($period as $date) {
                // Balance update
                $value = intval($value + $value * $this->faker->numberBetween(-50, 150) / 10000);
                $dto = new CreateTransactionDto();
                $dto->position = $position;
                $dto->valueDate = $date;
                $dto->value = $value;
                $dto->type = TransactionType::BALANCE_UPDATE;
                $this->messageBus->dispatch(new CreateTransactionCommand($dto));
            }
        }
        $this->positionCreditCardManager->flush();
    }

    public function getOrder(): int
    {
        return 20;
    }
}
