<?php

declare(strict_types=1);

namespace Groshy\Manager;

use Groshy\Entity\PositionValue;
use Talav\Component\Resource\Manager\ResourceManager;

class PositionValueManager extends ResourceManager
{
    public function upsert(PositionValue $positionValue): void
    {
        $existing = $this->getRepository()->findOneBy([
            'valueDate' => $positionValue->getValueDate(),
            'position' => $positionValue->getPosition(),
        ]);
        if (null !== $existing) {
            $this->remove($existing);
        }
        $this->add($positionValue);
    }
}
