<?php

namespace Groshy\Config;

use Groshy\Entity\AssetType;
use Talav\Component\Registry\Registry\ServiceRegistryInterface;

class ConfigProvider
{
    public function __construct(
        private readonly ServiceRegistryInterface $configModelRegistry,
        private readonly array $typeMap
    ) {
    }

    public function getConfig(AssetType $type): ConfigModel
    {
        if (!isset($this->typeMap[$type->getSlug()])) {
            throw new \RuntimeException(sprintf('Map for type %s is not defined', $type->getSlug()));
        }

        return $this->configModelRegistry->get($this->typeMap[$type->getSlug()]);
    }
}
