<?php

declare(strict_types=1);

namespace Groshy\Api\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Groshy\Entity\Transaction;

final class CreateTransactionTransformer implements DataTransformerInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function transform($data, string $to, array $context = [])
    {
        $object = $context['object_to_populate'] ?? false;
        if ($object && $object instanceof Transaction) {
            $data->id = $object->getId();
        }

        $this->validator->validate($data);

        return $data;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Transaction) {
            return false;
        }

        return Transaction::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
