<?php

declare(strict_types=1);

namespace Groshy\Api\Action\User;

use DateTime;
use Groshy\Entity\User;
use Groshy\Provider\DashboardProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Talav\Component\Resource\Repository\RepositoryInterface;

// https://stackoverflow.com/questions/62816024/how-to-implement-custom-item-get-endpoint-with-filtering-in-api-platform
class UserStatsAction
{
    public function __construct(
        private readonly DashboardProvider $provider,
        private readonly RepositoryInterface $positionRepository,
        private readonly RepositoryInterface $assetTypeRepository,
    ) {
    }

    public function __invoke(User $user, Request $request): JsonResponse
    {
        $from = DateTime::createFromFormat('Y-m-d', $request->query->get('from'));
        $to = DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $typeId = $request->query->get('type');
        $positionUuid = $request->query->get('position');
        $type = is_null($typeId) ? null : $this->assetTypeRepository->find($typeId);
        $position = is_null($positionUuid) ? null : $this->positionRepository->findOneBy(['uuid' => $positionUuid]);

        return new JsonResponse($this->provider->getDashboardData($from, $to, $user, $type, $position), 200);
    }
}
