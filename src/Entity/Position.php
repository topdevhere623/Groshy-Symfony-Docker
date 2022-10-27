<?php

declare(strict_types=1);

namespace Groshy\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\CreatedBy;
use Talav\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[DiscriminatorMap([
    'investment' => "Groshy\Entity\PositionInvestment",
    'cash' => "Groshy\Entity\PositionCash",
    'credit_card' => "Groshy\Entity\PositionCreditCard",
])]
#[Table(name: 'position')]
class Position implements ResourceInterface
{
    use ResourceTrait;
    use Timestampable;
    use CreatedBy;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['position:item:read', 'position:collection:read', 'position:cascade:read'])]
    protected mixed $id;

    #[Column(type: 'string', length: 250, nullable: true)]
    #[Groups(['position:item:read'])]
    protected ?string $notes = null;

    #[Column(type: 'date', nullable: true)]
    #[Groups(['position:item:read', 'position:collection:read'])]
    protected ?DateTime $startDate = null;

    #[Column(type: 'date', nullable: true)]
    #[Groups(['position:item:read', 'position:collection:read'])]
    protected ?DateTime $completeDate = null;

    #[Column(type: 'integer')]
    #[Groups(['position:item:read', 'position:collection:read'])]
    protected int $generatedIncome = 0;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    #[Groups(['position:item:read'])]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    #[Groups(['position:item:read'])]
    protected ?DateTime $updatedAt = null;

    #[ManyToOne(targetEntity: UserInterface::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    #[Groups(['position:item:read', 'position:collection:read', 'position:cascade:read'])]
    protected ?UserInterface $createdBy = null;

    #[ManyToOne(targetEntity: Asset::class)]
    #[JoinColumn(name: 'asset_id', referencedColumnName: 'id')]
    #[Groups(['position:item:read'])]
    protected ?Asset $asset = null;

    #[OneToOne(targetEntity: PositionValue::class)]
    #[JoinColumn(name: 'last_value_id', referencedColumnName: 'id')]
    #[Groups(['position:item:read'])]
    protected ?PositionValue $lastValue = null;

    #[ManyToMany(targetEntity: Tag::class)]
    #[JoinTable(name: 'position_tag')]
    #[JoinColumn(name: 'position_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    #[Groups(['position:item:read'])]
    protected Collection $tags;

    #[ManyToOne(targetEntity: Institution::class)]
    #[JoinColumn(name: 'institution_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['position:item:read', 'position:collection:read'])]
    protected ?Institution $institution = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getCompleteDate(): ?DateTime
    {
        return $this->completeDate;
    }

    public function setCompleteDate(?DateTime $completeDate): void
    {
        $this->completeDate = $completeDate;
    }

    public function isCompleted(): bool
    {
        return !is_null($this->getCompleteDate());
    }

    public function setAsset(Asset $asset): void
    {
        $this->asset = $asset;
    }

    public function getLastValue(): ?PositionValue
    {
        return $this->lastValue;
    }

    public function setLastValue(PositionValue $lastValue): void
    {
        if (is_null($this->startDate)) {
            $this->startDate = $lastValue->getValueDate();
        }
        $this->lastValue = $lastValue;
    }

    public function removeLastValue(): void
    {
        $this->lastValue = null;
    }

    public function getGeneratedIncome(): int
    {
        return $this->generatedIncome;
    }

    public function setGeneratedIncome(int $generatedIncome): void
    {
        $this->generatedIncome = $generatedIncome;
    }

    public function addTag(Tag $tag): void
    {
        Assert::eq($tag->getCreatedBy()->getId(), $this->getCreatedBy()->getId());
        $this->tags->add($tag);
    }

    public function addTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->tags->add($tag);
        }
    }

    public function setTags(array $tags): void
    {
        $this->tags->clear();
        $this->addTags($tags);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): void
    {
        $this->institution = $institution;
    }
}
