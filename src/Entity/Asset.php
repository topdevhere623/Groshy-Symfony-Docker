<?php

declare(strict_types=1);

namespace Groshy\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Groshy\Enum\Privacy;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\CreatedBy;
use Talav\Component\User\Model\UserInterface;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[DiscriminatorMap([
    'investment' => "Groshy\Entity\AssetInvestment",
    'cash' => "Groshy\Entity\AssetCash",
    'credit_card' => "Groshy\Entity\AssetCreditCard",
])]
#[Table(name: 'asset')]
class Asset implements ResourceInterface
{
    use ResourceTrait;
    use Timestampable;
    use CreatedBy;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['asset:item:read', 'asset:collection:read'])]
    protected mixed $id;

    #[Column(type: 'string', length: 250)]
    #[Groups(['asset:item:read', 'asset:collection:read'])]
    protected ?string $name;

    #[Column(type: 'string', length: 10, enumType: Privacy::class)]
    protected Privacy $privacy;

    #[Column(type: 'string', length: 250)]
    #[Gedmo\Slug(fields: ['name'])]
    protected ?string $slug;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    protected ?DateTime $updatedAt = null;

    #[ManyToOne(targetEntity: Sponsor::class, cascade: ['persist'])]
    #[JoinColumn(name: 'sponsor_id', referencedColumnName: 'id', nullable: true)]
    protected ?Sponsor $sponsor = null;

    #[ManyToOne(targetEntity: UserInterface::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    protected ?UserInterface $createdBy = null;

    #[ManyToOne(targetEntity: AssetType::class)]
    #[JoinColumn(name: 'asset_type_id', referencedColumnName: 'id', nullable: true)]
    protected ?AssetType $assetType = null;

    protected AssetConfig $config;

    public function __construct()
    {
        $this->config = $this->createConfig();
        $this->privacy = $this->config->getDefaultPrivacy();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrivacy(): Privacy
    {
        return $this->privacy;
    }

    public function setPrivacy(Privacy $privacy): void
    {
        if (!$this->config->isAllowPrivacyChange()) {
            throw new \RuntimeException('Privacy Policy change is not allowed for this asset');
        }
        $this->privacy = $privacy;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsor $sponsor): void
    {
        $this->sponsor = $sponsor;
    }

    public function getAssetType(): ?AssetType
    {
        return $this->assetType;
    }

    public function setAssetType(AssetType $assetType): void
    {
        $this->assetType = $assetType;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function createConfig(): AssetConfig
    {
        return new AssetConfig(Privacy::PUBLIC, true);
    }
}
