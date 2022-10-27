<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Entity\Position;
use Groshy\Entity\PositionValue;
use Groshy\Entity\Transaction;
use Groshy\Enum\TransactionType;
use Groshy\Message\Command\CreateTransactionCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class CreateTransactionHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AutoMapperInterface $mapper,
        private readonly ManagerInterface $transactionManager,
        private readonly ManagerInterface $positionValueManager,
        private readonly ManagerInterface $positionManager,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(CreateTransactionCommand $message)
    {
        $dto = $message->dto;
        /** @var Transaction $transaction */
        $transaction = $this->mapper->mapToObject($dto, $this->transactionManager->create());
        $this->transactionManager->update($transaction);

        switch ($transaction->getType()) {
            case TransactionType::CAPITAL_CALL:
            case TransactionType::VALUE_UPDATE:
            case TransactionType::BALANCE_UPDATE:
                $this->processValueUpdate($transaction);
                break;
            case TransactionType::DISTRIBUTION:
                if ($dto->isReinvested) {
                    $reinvested = $this->createReinvestedTransaction($transaction);
                    $this->increasePositionValueByDrip($reinvested);
                }
                $this->updateDistribution($transaction->getPosition());
                break;
            default: throw new \RuntimeException(sprintf('Transaction %s is not supported', $transaction->getType()->value));
        }
        $this->transactionManager->flush();

        return $transaction;
    }

    private function processValueUpdate(Transaction $transaction): void
    {
        $position = $transaction->getPosition();
        $value = $this->createValueFromTransaction($transaction);
        if (null === $position->getLastValue()) {
            // position has just been created, add value to it
            $position->setLastValue($value);
        } else {
            $this->updateLastValue($transaction->getPosition());
        }
    }

    private function createValueFromTransaction(Transaction $transaction): PositionValue
    {
        /** @var PositionValue $positionValue */
        $positionValue = $this->positionValueManager->create();
        $positionValue->setValue($transaction->getValue());
        $positionValue->setValueDate($transaction->getValueDate());
        $positionValue->setPosition($transaction->getPosition());
        $this->positionValueManager->upsert($positionValue);

        return $positionValue;
    }

    private function updateLastValue(Position $position): void
    {
        $assetValue = $this->positionValueManager->getRepository()->getLastByPosition($position);
        $position->setLastValue($assetValue);
        $this->positionManager->update($position);
    }

    private function createReinvestedTransaction(Transaction $transaction): Transaction
    {
        /** @var Transaction $reinvested */
        $reinvested = $this->transactionManager->create();
        $reinvested->setType(TransactionType::REINVEST);
        $reinvested->setValue($transaction->getValue());
        $reinvested->setValueDate($transaction->getValueDate());
        $reinvested->setPosition($transaction->getPosition());
        $this->transactionManager->update($reinvested);

        return $reinvested;
    }

    private function increasePositionValueByDrip(Transaction $transaction)
    {
        $positionRepository = $this->positionValueManager->getRepository();
        $assetValue = $positionRepository->findOneBy([
            'valueDate' => $transaction->getValueDate(),
            'position' => $transaction->getPosition(), ]);
        if (null !== $assetValue || null == $transaction->getPosition()->getLastValue()) {
            return;
        }
        $res = $positionRepository->getLastBeforeDateForPositions($transaction->getValueDate(), [$transaction->getPosition()->getId()]);
        if (0 == count($res)) {
            return;
        }
        $value = $this->createValueFromTransaction($transaction);
        $value->setValue($res[0]->getValue() + $transaction->getValue());
    }

    private function updateDistribution(Position $position)
    {
        $position->setGeneratedIncome(
            $this->transactionManager->getRepository()->sumByPositionAndType($position, TransactionType::getDistributionTypes())
        );
    }
}
