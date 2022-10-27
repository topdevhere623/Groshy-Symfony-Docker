<?php

declare(strict_types=1);

namespace Groshy\Api\Action\PositionInvestment;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Api\Dto\PositionInvestment\ApiUpdatePositionInvestmentDto;
use Groshy\Entity\PositionInvestment;
use Groshy\Message\Command\PositionInvestment\UpdatePositionInvestmentCommand;
use Groshy\Message\Dto\PositionInvestment\UpdatePositionInvestmentDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class UpdatePositionInvestment
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly AutoMapperInterface $mapper,
    ) {
    }

    public function __invoke(PositionInvestment $position, ApiUpdatePositionInvestmentDto $data): PositionInvestment
    {
        $dto = $this->mapper->map($data, UpdatePositionInvestmentDto::class);
        $envelope = $this->bus->dispatch(new UpdatePositionInvestmentCommand($position, $dto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
