<?php

namespace Groshy\Controller\Frontend;

use DateTime;
use Groshy\Config\ConfigProvider;
use Groshy\Entity\AssetType;
use Groshy\Provider\DashboardProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\Component\Resource\Repository\RepositoryInterface;

class AssetTypeController extends AbstractController
{
    public function __construct(
        private readonly DashboardProvider $provider,
        private readonly RepositoryInterface $positionRepository,
        private readonly RepositoryInterface $positionValueRepository,
        private readonly ConfigProvider $configProvider,
    ) {
    }

    /**
     * @Route("/user/assets/{slug}")
     */
    public function assetsAction(AssetType $type, Request $request): Response
    {
        $to = DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $from = DateTime::createFromFormat('Y-m-d', $request->query->get('from'));

        return $this->render('asset_type/assets.html.twig', [
            'assetType' => $type,
            'from' => $from,
            'to' => $to,
            'dash' => $this->provider->getDashboardData($from, $to, $this->getUser(), $type),
            'maxDate' => $this->positionValueRepository->getFirstDate($this->getUser(), $type),
            'investments' => $this->positionRepository->byType($type, $this->getUser()),
            'config' => $this->configProvider->getConfig($type),
        ]);
    }
}
