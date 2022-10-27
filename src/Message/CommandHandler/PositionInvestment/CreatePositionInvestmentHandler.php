<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler\PositionInvestment;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Entity\PositionInvestment;
use Groshy\Message\Command\PositionInvestment\CreatePositionInvestmentCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class CreatePositionInvestmentHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AutoMapperInterface $mapper,
        private readonly ManagerInterface $positionInvestmentManager
    ) {
    }

    public function __invoke(CreatePositionInvestmentCommand $message): PositionInvestment
    {
        $position = $this->mapper->mapToObject($message->dto, $this->positionInvestmentManager->create());
        $this->positionInvestmentManager->update($position);

        return $position;
    }
}
