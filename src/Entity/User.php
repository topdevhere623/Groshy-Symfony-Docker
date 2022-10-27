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
use Doctrine\ORM\Mapping\Table;
use Groshy\Api\Action\User\UserStatsAction;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\UserBundle\Entity\User as BaseUser;

#[Entity]
#[Table(name: 'user')]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'stats' => [
            'method' => 'GET',
            'path' => '/users/{id}/stats',
            'controller' => UserStatsAction::class,
            'openapi_context' => [
                'summary' => 'Returns a dashboard for the provided user',
                'parameters' => [
                    [
                        'name' => 'from',
                        'type' => DateTime::class,
                        'in' => 'query',
                        'description' => 'Start date for user stats',
                        'example' => '2022-05-05',
                    ],
                    [
                        'name' => 'to',
                        'type' => DateTime::class,
                        'in' => 'query',
                        'description' => 'End date for user stats',
                        'example' => '2022-05-05',
                    ],
                    [
                        'name' => 'type',
                        'type' => 'integer',
                        'in' => 'query',
                        'description' => 'Asset type id',
                        'example' => '1',
                    ],
                    [
                        'name' => 'position',
                        'type' => 'string',
                        'in' => 'query',
                        'description' => 'Position id UUID',
                        'example' => 'fb9be8d6-da20-4d0b-8284-d7553eaa61c8',
                    ],
                 ],
            ],
        ],
        'get',
    ]
)]
class User extends BaseUser
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    protected mixed $id = null;
}
