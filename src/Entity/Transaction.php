<?php

declare(strict_types=1);

namespace Groshy\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Groshy\Api\Action\Transaction\CreateTransactionAction;
use Groshy\Api\Dto\Transaction\ApiCreateTransactionDto;
use Groshy\Enum\TransactionType;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;

#[Entity]
#[Table(name: 'transaction')]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['transaction:collection:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
        'post' => [
            'controller' => CreateTransactionAction::class,
            'input' => ApiCreateTransactionDto::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['transaction:item:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
    ]
)]
class Transaction implements ResourceInterface
{
    use ResourceTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    protected mixed $id;

    #[Column(type: 'integer')]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    protected ?int $value;

    #[Column(type: 'date')]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    protected ?DateTime $valueDate;

    #[Column(type: 'string', enumType: TransactionType::class)]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    protected TransactionType $type;

    #[Column(type: 'text', nullable: true)]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    protected ?string $notes = null;

    #[ManyToOne(targetEntity: Position::class)]
    #[JoinColumn(name: 'position_id', nullable: false)]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    #[Context(context: ['groups' => 'position:cascade:read'])]
    protected ?Position $position;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    #[Groups(['transaction:item:read', 'transaction:collection:read'])]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    #[Groups(['transaction:item:read'])]
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

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }
}
