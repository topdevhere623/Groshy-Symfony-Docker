<?php

declare(strict_types=1);

namespace Groshy\Api\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Groshy\Entity\TagGroup;

final class TagGroupTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function transform($data, string $to, array $context = [])
    {
        $object = $context['object_to_populate'] ?? false;
        if ($object && $object instanceof TagGroup) {
            $data->id = $object->getId();
        }

        $this->validator->validate($data);

        return $data;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof TagGroup) {
            return false;
        }

        return TagGroup::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
