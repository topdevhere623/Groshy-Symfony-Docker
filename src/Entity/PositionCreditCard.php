<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class PositionCreditCard extends Position
{
    #[Column(type: 'object')]
    protected PositionCreditCardData $data;

    public function __construct()
    {
        $this->data = new PositionCreditCardData();
        parent::__construct();
    }

    public function getData(): PositionCreditCardData
    {
        return $this->data;
    }

    public function setData(PositionCreditCardData $data): void
    {
        $this->data = $data;
    }

    public function getUtilization(): ?float
    {
        if (is_null($this->data->getCardLimit()) || 0 == $this->data->getCardLimit()) {
            return null;
        }

        return $this->getLastValue()->getValue() / $this->data->getCardLimit() * 100;
    }
}
