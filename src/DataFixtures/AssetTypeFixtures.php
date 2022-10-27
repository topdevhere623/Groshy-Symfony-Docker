<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Entity\AssetType;
use Talav\Component\Resource\Manager\ManagerInterface;

final class AssetTypeFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly ManagerInterface $assetTypeManager,
    ) {
    }

    public function loadData(): void
    {
        foreach ($this->getData() as $data) {
            /** @var AssetType $type */
            $type = $this->assetTypeManager->create();
            $type->setName($data['name']);
            $type->setPosition($data['position']);
            $type->setIsAsset($data['is_asset']);
            $type->setIsActive($data['is_active']);
            $this->assetTypeManager->update($type);
            if (isset($data['children'])) {
                foreach ($data['children'] as $childData) {
                    /** @var AssetType $child */
                    $child = $this->assetTypeManager->create();
                    $child->setName($childData['name']);
                    $child->setPosition($childData['position']);
                    $child->setIsAsset($childData['is_asset']);
                    $child->setIsActive($childData['is_active']);
                    $child->setParent($type);
                    $this->assetTypeManager->update($child);
                }
            }
        }
        $this->assetTypeManager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }

    private function getData(): array
    {
        return [
            [
                'name' => 'Real Estate',
                'position' => 0,
                'is_asset' => true,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Hard Money Loan Fund',
                        'position' => 1,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Public Non Traded REIT',
                        'position' => 2,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Real Estate GP Fund',
                        'position' => 3,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Real Estate LP Fund',
                        'position' => 4,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Peer-to-Peer Lending',
                        'position' => 5,
                        'is_asset' => true,
                        'is_active' => false,
                    ],
                ],
            ],
            [
                'name' => 'Investment Property',
                'position' => 6,
                'is_asset' => true,
                'is_active' => false,
            ],
            [
                'name' => 'Private Equity',
                'position' => 7,
                'is_asset' => true,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Private Equity GP Fund',
                        'position' => 8,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Private Equity LP Fund',
                        'position' => 9,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Venture Capital',
                        'position' => 10,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Search Fund',
                        'position' => 11,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Secondaries',
                        'position' => 12,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'name' => 'Alternative Investment',
                'position' => 13,
                'is_asset' => true,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Litigation Financing',
                        'position' => 14,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Music Royalties',
                        'position' => 15,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Life Insurance Settlements',
                        'position' => 16,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Private Credit',
                        'position' => 17,
                        'is_asset' => true,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'name' => 'Public Equity',
                'position' => 18,
                'is_asset' => true,
                'is_active' => false,
            ],
            [
                'name' => 'Cash',
                'position' => 19,
                'is_asset' => true,
                'is_active' => false,
                'children' => [
                    [
                        'name' => 'Savings/Checking',
                        'position' => 20,
                        'is_asset' => true,
                        'is_active' => false,
                    ],
                    [
                        'name' => 'CD',
                        'position' => 21,
                        'is_asset' => true,
                        'is_active' => false,
                    ],
                ],
            ],
            [
                'name' => 'Cryptocurrency',
                'position' => 22,
                'is_asset' => true,
                'is_active' => false,
            ],
            [
                'name' => 'Private Business',
                'position' => 23,
                'is_asset' => true,
                'is_active' => false,
            ],
            [
                'name' => 'Collectables',
                'position' => 24,
                'is_asset' => true,
                'is_active' => false,
            ],
            [
                'name' => 'Loan',
                'position' => 25,
                'is_asset' => false,
                'is_active' => false,
            ],
            [
                'name' => 'Mortgage',
                'position' => 26,
                'is_asset' => false,
                'is_active' => false,
            ],
            [
                'name' => 'Credit Card',
                'position' => 27,
                'is_asset' => false,
                'is_active' => false,
            ],
        ];
    }
}
