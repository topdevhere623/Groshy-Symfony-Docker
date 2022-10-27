<?php

declare(strict_types=1);

namespace Groshy\Model;

use Groshy\Entity\AssetType;
use Groshy\Entity\Position;
use Groshy\Entity\User;

final class AttributeModel
{
    public function __construct(
        public readonly mixed $id,
        public readonly string $name,
    ) {
    }

    public static function fromPosition(Position $position): AttributeModel
    {
        return new AttributeModel($position->getId()->toString(), $position->getAsset()->getName());
    }

    public static function fromUser(User $user): AttributeModel
    {
        return new AttributeModel($user->getUsername(), $user->getUsername());
    }

    public static function fromType(AssetType $type): AttributeModel
    {
        return new AttributeModel($type->getSlug(), $type->getName());
    }
}
