<?php

declare(strict_types=1);

namespace Groshy\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;

#[Entity]
#[Table(name: 'asset_type')]
#[Index(columns: ['is_asset'])]
#[Index(columns: ['position'])]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['assetType:collection:read'],
                'swagger_definition_name' => 'Collection Read',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['assetType:item:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
    ],
)]
class AssetType implements ResourceInterface
{
    use ResourceTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['assetType:item:read', 'assetType:collection:read'])]
    protected mixed $id;

    #[Column(type: 'string', length: 250)]
    #[Groups(['assetType:item:read', 'assetType:collection:read'])]
    protected ?string $name;

    #[Column(type: 'integer')]
    protected int $position = 0;

    #[Column(name: 'is_asset', type: 'boolean')]
    protected bool $isAsset = true;

    #[Column(name:'is_active', type: 'boolean')]
    protected bool $isActive = true;

    #[Column(type: 'string', length: 250, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    #[Groups(['assetType:item:read', 'assetType:collection:read'])]
    protected ?string $slug;

    #[OneToMany(mappedBy: 'parent', targetEntity: AssetType::class)]
    protected Collection $children;

    #[ManyToOne(targetEntity: AssetType::class, inversedBy: 'children')]
    #[Groups(['assetType:item:read', 'assetType:collection:read'])]
    protected ?AssetType $parent;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isAsset(): bool
    {
        return $this->isAsset;
    }

    public function setIsAsset(bool $isAsset): void
    {
        $this->isAsset = $isAsset;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(ArrayCollection|Collection $children): void
    {
        $this->children = $children;
    }

    public function getParent(): ?AssetType
    {
        return $this->parent;
    }

    public function setParent(?AssetType $parent): void
    {
        $this->parent = $parent;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function isTopLevel(): bool
    {
        return is_null($this->getParent());
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
