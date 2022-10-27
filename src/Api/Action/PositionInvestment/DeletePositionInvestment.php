<?php

declare(strict_types=1);

namespace Groshy\Api\Action\PositionInvestment;

use Groshy\Message\Command\PositionInvestment\DeletePositionInvestmentCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class DeletePositionInvestment
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public function __invoke($data): void
    {
        $this->bus->dispatch(new DeletePositionInvestmentCommand($data));
    }
}
