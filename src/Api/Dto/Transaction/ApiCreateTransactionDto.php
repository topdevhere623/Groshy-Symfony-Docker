<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\Transaction;

use DateTime;
use Groshy\Entity\PositionCash;
use Groshy\Entity\PositionCreditCard;
use Groshy\Entity\PositionInvestment;
use Groshy\Enum\TransactionType;
use Symfony\Component\Validator\Constraints as Assert;

class ApiCreateTransactionDto
{
    #[Assert\NotBlank]
    public ?DateTime $valueDate = null;

    #[Assert\NotBlank]
    #[Assert\LessThan(value: 1000000)]
    public ?float $value = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TransactionType::class, 'choices'])]
    public ?string $type = null;

    #[Assert\NotBlank]
    public null|PositionInvestment|PositionCreditCard|PositionCash $position = null;

    #[Assert\NotNull]
    public bool $isReinvested = false;

    #[Assert\NotNull]
    public bool $isCompleted = false;
}
