<?php

declare(strict_types=1);

namespace Groshy\Entity;

class PositionCreditCardData
{
    protected ?int $cardLimit;

    public function getCardLimit(): ?int
    {
        return $this->cardLimit;
    }

    public function setCardLimit(?int $cardLimit): void
    {
        $this->cardLimit = $cardLimit;
    }
}
