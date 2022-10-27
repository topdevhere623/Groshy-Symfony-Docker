<?php

declare(strict_types=1);

namespace Groshy\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Groshy\Api\Dto\TagGroup\ApiCreateTagGroupDto;
use Groshy\Controller\Api\TagGroup\CreateTagGroupAction;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\CreatedBy;
use Talav\Component\User\Model\UserInterface;

#[Entity]
#[Table(name: 'tag_group')]
#[ApiResource(collectionOperations: [
    'create' => [
        'method' => 'POST',
        'controller' => CreateTagGroupAction::class,
        'input' => ApiCreateTagGroupDto::class,
    ],
])]
class TagGroup implements ResourceInterface
{
    use ResourceTrait;
    use Timestampable;
    use CreatedBy;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    protected mixed $id;

    #[Column(type: 'string', length: 250)]
    protected ?string $name;

    #[Column(type: 'integer')]
    protected int $position;

    #[Column(name: 'created_at', type: 'datetime', nullable: true)]
    #[GedmoTimestampable(on: 'create')]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime', nullable: true)]
    #[GedmoTimestampable(on: 'update')]
    protected ?DateTime $updatedAt = null;

    #[ManyToOne(targetEntity: UserInterface::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    protected ?UserInterface $createdBy = null;

    #[OneToMany(mappedBy: 'tagGroup', targetEntity: Tag::class)]
    protected Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->position = 0;
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

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->setTagGroup($this);
        }
    }
}
