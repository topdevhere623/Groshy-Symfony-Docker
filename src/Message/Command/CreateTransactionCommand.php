<?php

declare(strict_types=1);

namespace Groshy\Message\Command;

use Groshy\Message\Dto\Transaction\CreateTransactionDto;
use Talav\Component\Resource\Model\DomainEventInterface;

final class CreateTransactionCommand implements DomainEventInterface
{
    public function __construct(
        public CreateTransactionDto $dto
    ) {
    }
}
