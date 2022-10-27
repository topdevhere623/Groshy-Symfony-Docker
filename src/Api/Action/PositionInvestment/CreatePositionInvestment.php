<?php

declare(strict_types=1);

namespace Groshy\Api\Action\PositionInvestment;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Api\Dto\PositionInvestment\ApiCreatePositionInvestmentDto;
use Groshy\Entity\PositionInvestment;
use Groshy\Message\Command\PositionInvestment\CreatePositionInvestmentCommand;
use Groshy\Message\Dto\PositionInvestment\CreatePositionInvestmentDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Security;

class CreatePositionInvestment
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly AutoMapperInterface $mapper,
        private readonly Security $security,
    ) {
    }

    public function __invoke(ApiCreatePositionInvestmentDto $data): PositionInvestment
    {
        $dto = $this->mapper->map($data, CreatePositionInvestmentDto::class);
        $dto->createdBy = $this->security->getUser();
        $envelope = $this->bus->dispatch(new CreatePositionInvestmentCommand($dto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
