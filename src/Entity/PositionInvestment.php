<?php

declare(strict_types=1);

namespace Groshy\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Groshy\Api\Action\PositionInvestment\CreatePositionInvestment;
use Groshy\Api\Action\PositionInvestment\DeletePositionInvestment;
use Groshy\Api\Action\PositionInvestment\UpdatePositionInvestment;
use Groshy\Api\Dto\PositionInvestment\ApiCreatePositionInvestmentDto;
use Groshy\Api\Dto\PositionInvestment\ApiUpdatePositionInvestmentDto;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[ApiResource(
    collectionOperations: [
        'create' => [
            'method' => 'POST',
            'controller' => CreatePositionInvestment::class,
            'input' => ApiCreatePositionInvestmentDto::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['position:item:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
        'patch' => [
            'method' => 'PATCH',
            'controller' => UpdatePositionInvestment::class,
            'input' => ApiUpdatePositionInvestmentDto::class,
        ],
        'delete' => [
            'controller' => DeletePositionInvestment::class,
        ],
    ],
)]
class PositionInvestment extends Position
{
    #[Column(type: 'object')]
    #[Groups(['position:item:read'])]
    #[Context(context: ['groups' => []])]
    protected PositionInvestmentData $data;

    public function __construct()
    {
        $this->data = new PositionInvestmentData();
        parent::__construct();
    }

    public function getData(): PositionInvestmentData
    {
        return $this->data;
    }

    public function setData(PositionInvestmentData $data): void
    {
        $this->data = $data;
    }
}
