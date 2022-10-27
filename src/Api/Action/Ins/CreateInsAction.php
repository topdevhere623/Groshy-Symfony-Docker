<?php

declare(strict_types=1);

namespace Groshy\Api\Action\Ins;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Api\Dto\Ins\ApiCreateInsDto;
use Groshy\Entity\Institution;
use Groshy\Message\Command\CreateInsCommand;
use Groshy\Message\Dto\Ins\CreateInsDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Security;

class CreateInsAction
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly AutoMapperInterface $mapper,
        private readonly Security $security,
    ) {
    }

    public function __invoke(ApiCreateInsDto $data): Institution
    {
        $dto = $this->mapper->map($data, CreateInsDto::class);
        $dto->createdBy = $this->security->getUser();
        $envelope = $this->bus->dispatch(new CreateInsCommand($dto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
