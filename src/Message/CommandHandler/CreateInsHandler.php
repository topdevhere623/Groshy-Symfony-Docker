<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Message\Command\CreateInsCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class CreateInsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AutoMapperInterface $mapper,
        private readonly ManagerInterface $institutionManager,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(CreateInsCommand $message)
    {
        $ins = $this->mapper->mapToObject($message->dto, $this->institutionManager->create());
        $this->institutionManager->update($ins);

        return $ins;
    }
}
