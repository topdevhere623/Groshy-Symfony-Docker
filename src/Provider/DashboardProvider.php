<?php

declare(strict_types=1);

namespace Groshy\Provider;

use DateTime;
use Groshy\Entity\AssetType;
use Groshy\Entity\Position;
use Groshy\Model\Dashboard;
use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\Component\User\Model\UserInterface;

final class DashboardProvider
{
    public function __construct(
        private readonly RepositoryInterface $positionRepository,
        private readonly RepositoryInterface $positionValueRepository
    ) {
    }

    public function getDashboardData(DateTime $from, DateTime $to, UserInterface $user, ?AssetType $type = null, ?Position $position = null): array
    {
        if (null !== $position) {
            $positionIds = [$position->getId()];
        } else {
            $positionIds = $this->positionRepository->getIdsByInterval($from, $to, $user);
        }
        $result = $this->positionValueRepository->getByIntervalAndType($from, $to, $positionIds, $type);
        $initial = $this->positionValueRepository->getLastBeforeDateForPositions($from, $positionIds);

        return Dashboard::toDashData($initial, $result, $from);
    }
}
