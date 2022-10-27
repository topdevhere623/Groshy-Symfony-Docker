<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Entity\Asset;
use Groshy\Entity\AssetInvestment;
use Groshy\Entity\AssetType;
use Groshy\Entity\PositionInvestment;
use Groshy\Entity\PositionInvestmentData;
use Groshy\Enum\TransactionType;
use Groshy\Message\Command\CreateTransactionCommand;
use Groshy\Message\Dto\Transaction\CreateTransactionDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;

final class PositionFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public const REIT_TYPE = 'Public Non Traded REIT';

    public function __construct(
        private readonly UserManagerInterface $userManager,
        private readonly ManagerInterface $positionInvestmentManager,
        private readonly ManagerInterface $assetTypeManager,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function loadData(): void
    {
        $type = $this->assetTypeManager->getRepository()->findOneBy(['name' => self::REIT_TYPE]);
        foreach ($this->userManager->getRepository()->findAll() as $user) {
            if ('user0' == $user->getUsername()) {
                continue;
            }
            for ($i = 0; $i < rand(1, 30); ++$i) {
                if (0 == $i && in_array($user->getUsername(), ['user1', 'user2'])) {
                    $asset = $this->enforceReferenceType($type);
                } else {
                    $asset = $this->getRandomReference(AssetInvestment::class);
                }
                /** @var PositionInvestment $pos */
                $pos = $this->positionInvestmentManager->create();
                $data = new PositionInvestmentData();
                $committed = $this->faker->invested;
                $called = intval($committed * $this->getCalledPercent($asset));
                $data->setCapitalCommitment($committed);
                $data->setCapitalCalled($called);
                $pos->setData($data);
                $pos->setCreatedBy($user);
                $pos->setAsset($asset);
                $this->positionInvestmentManager->update($pos);

                $dto = new CreateTransactionDto();
                $dto->position = $pos;
                $dto->valueDate = $this->faker->dateTimeBetween('-5 years', '-2 months');
                $dto->value = $called;
                $dto->type = TransactionType::CAPITAL_CALL;
                $this->messageBus->dispatch(new CreateTransactionCommand($dto));
            }
        }

        $this->positionInvestmentManager->flush();
    }

    public function getOrder(): int
    {
        return 20;
    }

    private function enforceReferenceType(AssetType $type): Asset
    {
        $ref = $this->getRandomReference(AssetInvestment::class);
        while ($ref->getAssetType() != $type) {
            $ref = $this->getRandomReference(AssetInvestment::class);
        }

        return $ref;
    }

    // REITs always call 100%
    private function getCalledPercent(Asset $asset): float
    {
        if (self::REIT_TYPE == $asset->getAssetType()->getName()) {
            return 1;
        }

        return $this->faker->numberBetween(95, 100) / 100;
    }
}
