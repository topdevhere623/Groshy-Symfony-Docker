<?php

declare(strict_types=1);

namespace Groshy\Api\Action\TagGroup;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Api\Dto\TagGroup\ApiCreateTagGroupDto;
use Groshy\Entity\TagGroup;
use Groshy\Message\Command\CreateTagGroupCommand;
use Groshy\Message\Dto\TagGroup\CreateTagGroupDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CreateTagGroupAction
{
    private MessageBusInterface $bus;

    private AutoMapperInterface $mapper;

    public function __construct(MessageBusInterface $bus, AutoMapperInterface $mapper)
    {
        $this->bus = $bus;
        $this->mapper = $mapper;
    }

    public function __invoke(ApiCreateTagGroupDto $data): TagGroup
    {
        $dto = $this->mapper->map($data, CreateTagGroupDto::class);
        $envelope = $this->bus->dispatch(new CreateTagGroupCommand($dto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
