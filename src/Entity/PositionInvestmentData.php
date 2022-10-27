<?php

declare(strict_types=1);

namespace Groshy\Entity;

class PositionInvestmentData
{
    protected int $capitalCommitment = 0;

    protected int $capitalCalled = 0;

    protected ?float $irr = null;

    protected ?float $multiplier = null;

    protected bool $isDirect = false;

    public function getCapitalCommitment(): int
    {
        return $this->capitalCommitment;
    }

    public function setCapitalCommitment(int $capitalCommitment): void
    {
        $this->capitalCommitment = $capitalCommitment;
    }

    public function getCapitalCalled(): ?int
    {
        return $this->capitalCalled;
    }

    public function setCapitalCalled(int $capitalCalled): void
    {
        $this->capitalCalled = $capitalCalled;
    }

    public function getIrr(): ?float
    {
        return $this->irr;
    }

    public function setIrr(float $irr): void
    {
        $this->irr = $irr;
    }

    public function getMultiplier(): ?float
    {
        return $this->multiplier;
    }

    public function setMultiplier(float $multiplier): void
    {
        $this->multiplier = $multiplier;
    }

    public function isDirect(): bool
    {
        return $this->isDirect;
    }

    public function setIsDirect(bool $isDirect): void
    {
        $this->isDirect = $isDirect;
    }
}
