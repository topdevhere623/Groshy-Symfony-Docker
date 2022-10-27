<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping\Entity;
use Groshy\Enum\Privacy;

#[Entity]
class AssetCreditCard extends Asset
{
    public function createConfig(): AssetConfig
    {
        return new AssetConfig(Privacy::PRIVATE, false);
    }
}
