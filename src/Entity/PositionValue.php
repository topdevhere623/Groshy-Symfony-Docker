<?php

declare(strict_types=1);

namespace Groshy\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;

#[Entity]
#[Table(name: 'position_value')]
#[UniqueConstraint(columns: ['value_date', 'position_id'])]
class PositionValue implements ResourceInterface
{
    use ResourceTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    protected mixed $id;

    #[Column(type: 'integer')]
    protected ?int $value;

    #[Column(type: 'date')]
    protected ?DateTime $valueDate;

    #[ManyToOne(targetEntity: Position::class)]
    #[JoinColumn(name: 'position_id')]
    protected ?Position $position;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    protected ?DateTime $updatedAt = null;

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getValueDate(): ?DateTime
    {
        return $this->valueDate;
    }

    public function setValueDate(DateTime $valueDate): void
    {
        $this->valueDate = $valueDate;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }
}
