<?php

declare(strict_types=1);

namespace Groshy\Enum;

enum TransactionType: string
{
    case INITIAL_BALANCE = 'Initial Balance';
    case BUY = 'Buy';
    case SELL = 'Sell';
    case DIVIDEND = 'Dividend';
    case VALUE_UPDATE = 'Value Update';
    case CAPITAL_CALL = 'Capital Call';
    case DISTRIBUTION = 'Distribution';
    case REINVEST = 'Reinvest Cash Income';
    case DEPOSIT = 'Deposit';
    case WITHDRAW = 'Withdraw';
    case INTEREST = 'Interest';
    case BALANCE_UPDATE = 'Balance Update';

    public static function getDistributionTypes(): array
    {
        return [
            TransactionType::DISTRIBUTION,
            TransactionType::INTEREST,
            TransactionType::DIVIDEND,
        ];
    }

    public static function choices(): array
    {
        return array_map(static fn (TransactionType $type): string => $type->value, TransactionType::cases());
    }
}
