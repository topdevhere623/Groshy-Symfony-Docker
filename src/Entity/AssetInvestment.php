<?php

declare(strict_types=1);

namespace Groshy\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Groshy\Api\Action\Asset\CreateAssetInvestmentAction;
use Groshy\Api\Dto\AssetInvestment\ApiCreateAssetInvestmentDto;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[ApiResource(
    collectionOperations: [
        'create' => [
            'method' => 'POST',
            'controller' => CreateAssetInvestmentAction::class,
            'input' => ApiCreateAssetInvestmentDto::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['asset:item:read'],
                'swagger_definition_name' => 'Item Read',
            ],
        ],
    ],
)]
class AssetInvestment extends Asset
{
    #[Column(type: 'object', nullable: false)]
    #[Groups(['asset:item:read', 'asset:collection:read'])]
    #[Context(context: ['groups' => []])]
    protected AssetInvestmentData $data;

    public function __construct()
    {
        $this->data = new AssetInvestmentData();
        parent::__construct();
    }

    public function getData(): AssetInvestmentData
    {
        return $this->data;
    }

    public function setData(AssetInvestmentData $data): void
    {
        $this->data = $data;
    }
}
