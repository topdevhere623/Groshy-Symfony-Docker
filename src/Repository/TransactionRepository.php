<?php

declare(strict_types=1);

namespace Groshy\Repository;

use Groshy\Entity\AssetType;
use Groshy\Entity\Position;
use Pagerfanta\Pagerfanta;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;

final class TransactionRepository extends ResourceRepository
{
    public function byType(AssetType $type, UserInterface $user): array
    {
        return $this->createQueryBuilder('transaction')
            ->select(['transaction', 'position', 'asset'])
            ->leftJoin('transaction.position', 'position')
            ->leftJoin('position.asset', 'asset')
            ->leftJoin('asset.assetType', 'assetType')
            ->andWhere('asset.assetType = :assetType OR assetType.parent = :assetType')
            ->andWhere('position.createdBy = :user')
            ->setParameter('assetType', $type)
            ->setParameter('user', $user)
            ->orderBy('transaction.valueDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function byUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('transaction')
            ->select(['transaction'])
            ->leftJoin('transaction.position', 'position')
            ->andWhere('position.createdBy = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function byPositionPager(Position $position, UserInterface $user): Pagerfanta
    {
        return $this->getPaginator($this->createQueryBuilder('transaction')
            ->select(['transaction', 'position', 'asset'])
            ->leftJoin('transaction.position', 'position')
            ->leftJoin('position.asset', 'asset')
            ->andWhere('position.createdBy = :user')
            ->andWhere('transaction.position = :position')
            ->setParameter('position', $position)
            ->setParameter('user', $user)
            ->orderBy('transaction.valueDate', 'DESC'));
    }

    public function sumByPositionAndType(Position $position, array $types): int
    {
        return (int) $this->createQueryBuilder('transaction')
            ->select(['SUM(transaction.value)'])
            ->andWhere('transaction.position = :position')
            ->andWhere('transaction.type IN (:types)')
            ->setParameter('position', $position)
            ->setParameter('types', $types)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteByPosition(Position $position): void
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->delete($this->_entityName, 'transaction')
            ->andWhere('transaction.position = :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->execute();
    }
}
