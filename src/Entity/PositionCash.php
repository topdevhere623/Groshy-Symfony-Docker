<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class PositionCash extends Position
{
    #[Column(type: 'object')]
    protected PositionCashData $data;
}
