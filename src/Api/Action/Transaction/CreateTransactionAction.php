<?php

declare(strict_types=1);

namespace Groshy\Api\Action\Transaction;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Api\Dto\Transaction\ApiCreateTransactionDto;
use Groshy\Entity\Transaction;
use Groshy\Message\Command\CreateTransactionCommand;
use Groshy\Message\Dto\Transaction\CreateTransactionDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CreateTransactionAction
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly AutoMapperInterface $mapper,
    ) {
    }

    public function __invoke(ApiCreateTransactionDto $data): Transaction
    {
        $dto = $this->mapper->map($data, CreateTransactionDto::class);
        $envelope = $this->bus->dispatch(new CreateTransactionCommand($dto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
