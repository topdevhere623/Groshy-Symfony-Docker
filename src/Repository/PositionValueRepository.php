<?php

declare(strict_types=1);

namespace Groshy\Repository;

use DateTime;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Groshy\Entity\AssetType;
use Groshy\Entity\Position;
use Groshy\Entity\PositionValue;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;

final class PositionValueRepository extends ResourceRepository
{
    use FilterByAssetTypeTrait;

    public function getByInterval(DateTime $from, DateTime $to, array $positionIds): iterable
    {
        return $this->createQueryBuilder('value')
            ->select(['value', 'position'])
            ->leftJoin('value.position', 'position')
            ->andWhere('position.id IN (:ids)')
            ->andWhere('value.valueDate >= :from')
            ->andWhere('value.valueDate <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('ids', $positionIds)
            ->orderBy('value.valueDate')
            ->getQuery()
            ->getResult();
    }

    public function getByIntervalAndType(DateTime $from, DateTime $to, array $positionIds, ?AssetType $type = null): iterable
    {
        $query = $this->createQueryBuilder('value')
            ->select(['value', 'position'])
            ->leftJoin('value.position', 'position')
            ->andWhere('position.id IN (:ids)')
            ->andWhere('value.valueDate >= :from')
            ->andWhere('value.valueDate <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('ids', $positionIds)
            ->orderBy('value.valueDate');

        return $this->filterByAssetType($query, $type)->getQuery()->getResult();
    }

    public function getFirstDate(UserInterface $user, ?AssetType $type = null, ?Position $position = null): ?DateTime
    {
        $query = $this->createQueryBuilder('value')
            ->leftJoin('value.position', 'position')
            ->andWhere('position.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('value.valueDate')
            ->setMaxResults(1);
        $query = $this->filterByAssetType($query, $type);
        if (!is_null($position)) {
            $query->andWhere('value.position = :position')
                ->setParameter('position', $position);
        }
        $result = $query->getQuery()->getOneOrNullResult();
        if (is_null($result)) {
            return null;
        }

        return $result->getValueDate();
    }

    public function getLastByPosition(Position $position): ?PositionValue
    {
        return $this->createQueryBuilder('value')
            ->select(['value'])
            ->andWhere('value.position = :position')
            ->orderBy('value.valueDate', 'DESC')
            ->setParameter('position', $position)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Returns a list of last values for every position before the specific date.
    // This need to set initial values for every position for graph building
    public function getLastBeforeDateForPositions(DateTime $to, array $positionIds): iterable
    {
        // can be optimized for 1 position id
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addEntityResult('Groshy\Entity\PositionValue', 'v');
        $rsm->addFieldResult('v', 'id', 'id');

        $query = $this->_em->createNativeQuery('
            WITH t1 AS ( 
                SELECT id, position_id, value_date, value, RANK() OVER (
                    PARTITION BY position_id 
                    ORDER BY value_date DESC, value DESC 
                ) AS rank FROM position_value WHERE value_date <= :to AND position_id IN (:ids)
            ) SELECT id FROM t1 WHERE rank = 1', $rsm);
        $query->setParameter('to', $to);
        $query->setParameter('ids', $positionIds);

        // run regular query to avoid working with partial objects
        return $this->createQueryBuilder('value')
            ->select(['value', 'position'])
            ->leftJoin('value.position', 'position')
            ->andWhere('value.id IN (:ids)')
            ->setParameter('ids', $query->getSingleColumnResult())
            ->getQuery()
            ->getResult();
    }

    public function deleteByPosition(Position $position): void
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->delete($this->_entityName, 'value')
            ->andWhere('value.position = :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->execute();
    }
}
