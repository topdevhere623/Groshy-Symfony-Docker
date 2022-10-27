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
use Groshy\Enum\Color;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\CreatedBy;
use Talav\Component\User\Model\UserInterface;

#[Entity]
#[Table(name: 'tag')]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['tag:collection:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
    ],
    itemOperations: [
        'get',
    ]
)]
class Tag implements ResourceInterface
{
    use ResourceTrait;
    use Timestampable;
    use CreatedBy;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['tag:item:read', 'tag:collection:read'])]
    protected mixed $id;

    #[Column(type: 'string', length: 250)]
    #[Groups(['tag:item:read', 'tag:collection:read'])]
    protected ?string $name;

    #[Column(type: 'integer')]
    #[Groups(['tag:item:read', 'tag:collection:read'])]
    protected int $position;

    #[Column(type: 'string', enumType: Color::class)]
    #[Groups(['tag:item:read', 'tag:collection:read'])]
    protected Color $color;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    protected ?DateTime $updatedAt = null;

    #[ManyToOne(targetEntity: UserInterface::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    protected ?UserInterface $createdBy = null;

    #[ManyToOne(targetEntity: TagGroup::class)]
    #[Groups(['tag:item:read', 'tag:collection:read'])]
    protected ?TagGroup $tagGroup;

    public function __construct()
    {
        $this->position = 0;
        $this->color = Color::ORANGE;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): void
    {
        $this->color = $color;
    }

    public function getTagGroup(): ?TagGroup
    {
        return $this->tagGroup;
    }

    public function setTagGroup(TagGroup $tagGroup): void
    {
        $this->tagGroup = $tagGroup;
        $tagGroup->addTag($this);
    }
}
