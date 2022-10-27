<?php

declare(strict_types=1);

namespace Groshy\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
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
use Groshy\Api\Action\Sponsor\CreateSponsorAction;
use Groshy\Api\Dto\Sponsor\ApiCreateSponsorDto;
use Groshy\Enum\Privacy;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\CreatedBy;
use Talav\Component\User\Model\UserInterface;

#[Entity]
#[Table(name: 'sponsor')]
#[ApiResource(
    collectionOperations: [
        'create' => [
            'method' => 'POST',
            'controller' => CreateSponsorAction::class,
            'input' => ApiCreateSponsorDto::class,
        ],
        'get' => [
            'normalization_context' => [
                'groups' => ['sponsor:collection:read'],
                'swagger_definition_name' => 'Collection Read',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['sponsor:item:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(SearchFilter::class, properties: ['privacy' => 'exact'])]
class Sponsor implements ResourceInterface
{
    use ResourceTrait;
    use Timestampable;
    use CreatedBy;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['sponsor:item:read', 'sponsor:collection:read'])]
    protected mixed $id;

    #[Column(type: 'string', length: 250)]
    #[Groups(['sponsor:item:read', 'sponsor:collection:read'])]
    protected ?string $name;

    #[Column(type: 'string', length: 250, nullable: true)]
    #[Groups(['sponsor:item:read', 'sponsor:collection:read'])]
    protected ?string $website;

    #[Column(type: 'string', length: 10, enumType: Privacy::class)]
    #[Groups(['sponsor:item:read', 'sponsor:collection:read'])]
    protected Privacy $privacy;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    #[Groups(['sponsor:item:read'])]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    #[Groups(['sponsor:item:read'])]
    protected ?DateTime $updatedAt = null;

    #[ManyToOne(targetEntity: UserInterface::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    protected ?UserInterface $createdBy = null;

    public function __construct()
    {
        $this->privacy = Privacy::PUBLIC;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getPrivacy(): Privacy
    {
        return $this->privacy;
    }

    public function setPrivacy(Privacy $privacy): void
    {
        $this->privacy = $privacy;
    }
}
