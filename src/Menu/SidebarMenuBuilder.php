<?php

declare(strict_types=1);

namespace Groshy\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Security;
use Talav\Component\Resource\Repository\RepositoryInterface;

final class SidebarMenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly RepositoryInterface $assetTypeRepository,
        private readonly RepositoryInterface $positionRepository,
        private readonly Security $security,
    ) {
    }

    public function createAssetSidebarMenu(array $options): object
    {
        return $this->buildMenu(
            $this->assetTypeRepository->getSidebarMenu(true)
        );
    }

    public function createLiabilitiesSidebarMenu(array $options): object
    {
        return $this->buildMenu(
            $this->assetTypeRepository->getSidebarMenu(false)
        );
    }

    public function buildMenu(array $items): object
    {
        $menu = $this->factory->createItem('root');
        $owned = $this->getOwnedTypes();
        foreach ($items as $type) {
            if (!isset($owned[strval($type->getId())])) {
                continue;
            }
            if (0 == $type->getChildren()->count()) {
                $menu->addChild($type->getName(), [
                    'route' => 'groshy_frontend_assettype_assets',
                    'routeParameters' => ['slug' => $type->getSlug()],
                ]);
            } else {
                $typeMain = $menu->addChild($type->getName());
                foreach ($type->getChildren() as $child) {
                    if (!isset($owned[strval($child->getId())])) {
                        continue;
                    }
                    $typeMain
                        ->addChild($child->getName(), [
                            'route' => 'groshy_frontend_assettype_assets',
                            'routeParameters' => ['slug' => $child->getSlug()],
                        ]);
                }
            }
        }

        return $menu;
    }

    private function getOwnedTypes(): array
    {
        $return = [];
        $user = $this->security->getUser();
        $types = $this->assetTypeRepository->findBy(['id' => $this->positionRepository->getAssetTypeId($user)]);
        foreach ($types as $type) {
            $return[strval($type->getId())] = 1;
            if (!is_null($type->getParent())) {
                $return[strval($type->getParent()->getId())] = 1;
            }
        }

        return $return;
    }
}
