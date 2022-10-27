<?php

declare(strict_types=1);

namespace Groshy\Message\Dto\Transaction;

use DateTime;
use Groshy\Entity\Position;
use Groshy\Enum\TransactionType;

class CreateTransactionDto
{
    public ?DateTime $valueDate = null;

    public ?int $value = null;

    public ?TransactionType $type = null;

    public ?Position $position = null;

    public bool $isReinvested = false;

    public bool $isCompleted = false;
}
