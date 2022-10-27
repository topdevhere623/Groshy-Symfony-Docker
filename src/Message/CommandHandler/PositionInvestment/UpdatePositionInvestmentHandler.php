<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler\PositionInvestment;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Entity\PositionInvestment;
use Groshy\Message\Command\PositionInvestment\UpdatePositionInvestmentCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class UpdatePositionInvestmentHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AutoMapperInterface $mapper,
        private readonly ManagerInterface $positionInvestmentManager
    ) {
    }

    public function __invoke(UpdatePositionInvestmentCommand $message): PositionInvestment
    {
        $this->mapper->mapToObject($message->dto, $message->position);
        $this->positionInvestmentManager->update($message->position, true);

        return $message->position;
    }
}
