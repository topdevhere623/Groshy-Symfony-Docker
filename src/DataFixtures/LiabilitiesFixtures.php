<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Entity\AssetCreditCard;
use Talav\Component\Resource\Manager\ManagerInterface;

final class LiabilitiesFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly ManagerInterface $assetCreditCardManager,
        private readonly ManagerInterface $assetTypeManager
    ) {
    }

    public function loadData(): void
    {
        /** @var AssetCreditCard $liability */
        $liability = $this->assetCreditCardManager->create();
        $liability->setName('Credit Card');
        $liability->setAssetType($this->assetTypeManager->getRepository()->findOneBy(['name' => 'Credit Card']));
        $this->assetCreditCardManager->update($liability, true);
    }

    public function getOrder(): int
    {
        return 5;
    }
}
