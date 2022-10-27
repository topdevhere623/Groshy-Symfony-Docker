<?php

declare(strict_types=1);

namespace Groshy\Repository;

use DateTime;
use Groshy\Entity\AssetType;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;

final class PositionRepository extends ResourceRepository
{
    use FilterByAssetTypeTrait;

    public function byType(AssetType $type, UserInterface $user): array
    {
        return $this->createQueryBuilder('position')
            ->select(['position'])
            ->leftJoin('position.asset', 'asset')
            ->leftJoin('asset.assetType', 'assetType')
            ->andWhere('asset.assetType = :assetType OR assetType.parent = :assetType')
            ->andWhere('position.createdBy = :user')
            ->setParameter('assetType', $type)
            ->setParameter('user', $user)
            ->orderBy('position.startDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // List of ids of active positions in the time frame
    public function getIdsByInterval(DateTime $from, DateTime $to, UserInterface $user, ?AssetType $type = null): array
    {
        $query = $this->createQueryBuilder('position')
            ->select(['position.id'])
            ->where('position.createdBy = :user')
            ->andWhere('position.startDate <= :to')
            ->andWhere('position.completeDate >= :from OR position.completeDate IS NULL')
            ->setParameter('user', $user)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        return $this->filterByAssetType($query, $type)->getQuery()->getSingleColumnResult();
    }

    public function groupBySponsor(UserInterface $user): array
    {
        return $this->createQueryBuilder('position')
            ->select(['sponsor.name', 'SUM(value.value) as total'])
            ->leftJoin('position.asset', 'asset')
            ->leftJoin('position.lastValue', 'value')
            ->leftJoin('asset.sponsor', 'sponsor')
            ->andWhere('position.createdBy = :user')
            ->setParameter('user', $user)
            ->groupBy('sponsor.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function groupByYear(UserInterface $user): array
    {
        return $this->createQueryBuilder('position')
            ->select(['YEAR(position.startDate) as year', 'SUM(value.value) as total'])
            ->leftJoin('position.lastValue', 'value')
            ->andWhere('position.createdBy = :user')
            ->setParameter('user', $user)
            ->groupBy('year')
            ->orderBy('year')
            ->getQuery()
            ->getArrayResult();
    }

    public function getAssetTypeId(UserInterface $user): array
    {
        return $this->createQueryBuilder('position')
            ->select(['assetType.id'])
            ->leftJoin('position.asset', 'asset')
            ->leftJoin('asset.assetType', 'assetType')
            ->andWhere('position.createdBy = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleColumnResult();
    }
}
