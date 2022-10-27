<?php

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
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Groshy\Api\Action\Ins\CreateInsAction;
use Groshy\Api\Dto\Ins\ApiCreateInsDto;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\CreatedBy;
use Talav\Component\User\Model\UserInterface;

#[Entity]
#[Table(name: 'institution')]
#[ApiResource(
    collectionOperations: [
        'create' => [
            'method' => 'POST',
            'controller' => CreateInsAction::class,
            'input' => ApiCreateInsDto::class,
        ],
        'get',
    ],
    itemOperations: [
        'get',
    ],
    normalizationContext: [
        'groups' => ['ins:read'],
        'swagger_definition_name' => 'Read',
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Institution implements ResourceInterface
{
    use ResourceTrait;
    use Timestampable;
    use CreatedBy;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['ins:read'])]
    protected mixed $id;

    #[Column(type: 'string', length: 250)]
    #[Groups(['ins:read'])]
    protected ?string $name;

    #[Column(type: 'string', length: 250)]
    #[Groups(['ins:read'])]
    protected ?string $website;

    #[Column(type: 'string', length: 250)]
    #[Gedmo\Slug(fields: ['name'])]
    protected ?string $slug;

    #[Column(name: 'created_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'create')]
    #[Groups(['ins:read'])]
    protected ?DateTime $createdAt = null;

    #[Column(name: 'updated_at', type: 'datetime')]
    #[GedmoTimestampable(on: 'update')]
    #[Groups(['ins:read'])]
    protected ?DateTime $updatedAt = null;

    #[ManyToOne(targetEntity: UserInterface::class)]
    #[JoinColumn(name: 'created_by', referencedColumnName: 'id', nullable: true)]
    protected ?UserInterface $createdBy = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }
}
