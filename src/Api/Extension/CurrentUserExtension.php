<?php

namespace Groshy\Api\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Groshy\Entity\AssetCash;
use Groshy\Entity\AssetCreditCard;
use Groshy\Entity\AssetInvestment;
use Groshy\Entity\PositionCash;
use Groshy\Entity\PositionCreditCard;
use Groshy\Entity\PositionInvestment;
use Groshy\Entity\PositionValue;
use Groshy\Entity\Sponsor;
use Groshy\Entity\Tag;
use Groshy\Entity\TagGroup;
use Groshy\Entity\Transaction;
use Groshy\Enum\Privacy;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private array $classesPrivacy = [
        Sponsor::class,
        AssetInvestment::class,
        AssetCash::class,
        AssetCreditCard::class,
    ];

    private array $classesOwner = [
        PositionInvestment::class,
        PositionCash::class,
        PositionCreditCard::class,
        Tag::class,
        TagGroup::class,
    ];

    private array $positionOwner = [
        Transaction::class,
        PositionValue::class,
    ];

    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (!in_array($resourceClass, array_merge($this->classesPrivacy, $this->classesOwner, $this->positionOwner)) || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        if (in_array($resourceClass, $this->classesPrivacy)) {
            $queryBuilder->andWhere(sprintf('%s.createdBy = :user OR %s.privacy = :public', $rootAlias, $rootAlias))
                ->setParameter('public', Privacy::PUBLIC);
        } elseif (in_array($resourceClass, $this->classesOwner)) {
            $queryBuilder->andWhere(sprintf('%s.createdBy = :user', $rootAlias));
        } else {
            $queryBuilder->leftJoin(sprintf('%s.position', $rootAlias), 'position')
                ->andWhere('position.createdBy = :user');
        }
        $queryBuilder->setParameter('user', $user);
    }
}
