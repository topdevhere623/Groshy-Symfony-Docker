<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Message\Command\CreateTagGroupCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class CreateTagGroupHandler implements MessageHandlerInterface
{
    public function __construct(
        private AutoMapperInterface $mapper,
        private ManagerInterface $tagGroupManager,
        private MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(CreateTagGroupCommand $message)
    {
        $tagGroup = $this->mapper->mapToObject($message->dto, $this->tagGroupManager->create());
        $this->tagGroupManager->update($tagGroup, true);

        return $tagGroup;
    }
}
