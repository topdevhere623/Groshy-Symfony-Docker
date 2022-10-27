<?php

declare(strict_types=1);

namespace Groshy\Menu;

use Groshy\Entity\AssetType;
use Groshy\Entity\Position;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Talav\Component\Resource\Repository\RepositoryInterface;

final class BreadcrumbsMenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly RepositoryInterface $assetTypeRepository
    ) {
    }

    public function buildDashboardMenu(array $options): object
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Dashboard');

        return $menu;
    }

    public function buildAssetTypeMenu(array $options): object
    {
        /** @var AssetType $type */
        $type = $options['type'];
        $menu = $this->createDashboardNode();
        if (!$type->isTopLevel()) {
            $this->addAssetTypeNode($menu, $type->getParent());
        }
        $menu->addChild($type->getName());

        return $menu;
    }

    public function buildPositionMenu(array $options): object
    {
        /** @var Position $position */
        $position = $options['position'];
        $type = $position->getAsset()->getAssetType();
        $menu = $this->createDashboardNode();
        if (!$type->isTopLevel()) {
            $this->addAssetTypeNode($menu, $type->getParent());
        }
        $this->addAssetTypeNode($menu, $type);
        $menu->addChild($position->getAsset()->getName());

        return $menu;
    }

    private function createDashboardNode(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Dashboard', [
            'route' => 'groshy_frontend_dashboard_dashboard',
        ]);

        return $menu;
    }

    private function addAssetTypeNode(ItemInterface $menu, AssetType $type)
    {
        $menu->addChild($type->getName(), [
            'route' => 'groshy_frontend_assettype_assets',
            'routeParameters' => ['slug' => $type->getSlug()],
        ]);
    }
}
