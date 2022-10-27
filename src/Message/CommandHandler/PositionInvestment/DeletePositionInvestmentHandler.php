<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler\PositionInvestment;

use Groshy\Message\Command\PositionInvestment\DeletePositionInvestmentCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\Resource\Repository\RepositoryInterface;

final class DeletePositionInvestmentHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly ManagerInterface $positionInvestmentManager,
        private readonly RepositoryInterface $transactionRepository,
        private readonly RepositoryInterface $positionValueRepository,
    ) {
    }

    public function __invoke(DeletePositionInvestmentCommand $message): void
    {
        $position = $message->position;
        $position->removeLastValue();
        $this->positionInvestmentManager->flush();
        $this->transactionRepository->deleteByPosition($position);
        $this->positionValueRepository->deleteByPosition($position);
        $this->positionInvestmentManager->remove($position);
        $this->positionInvestmentManager->flush();
    }
}
