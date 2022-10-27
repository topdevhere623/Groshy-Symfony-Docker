<?php

namespace Groshy\Controller\Frontend;

use DateTime;
use Groshy\Config\ConfigProvider;
use Groshy\Entity\Position;
use Groshy\Provider\DashboardProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\Component\Resource\Repository\RepositoryInterface;

class PositionController extends AbstractController
{
    public function __construct(
        private readonly DashboardProvider $provider,
        private readonly RepositoryInterface $transactionRepository,
        private readonly RepositoryInterface $positionValueRepository,
        private readonly ConfigProvider $configProvider,
    ) {
    }

    /**
     * @Route("/user/position/{uuid}")
     */
    public function positionAction(Position $position, Request $request): Response
    {
        $to = DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $from = DateTime::createFromFormat('Y-m-d', $request->query->get('from'));

        return $this->render('position/position.html.twig', [
            'position' => $position,
            'from' => $from,
            'to' => $to,
            'dash' => $this->provider->getDashboardData($from, $to, $this->getUser(), null, $position),
            'maxDate' => $this->positionValueRepository->getFirstDate($this->getUser(), null, $position),
            'transactions' => $this->transactionRepository->byPositionPager($position, $this->getUser()),
            'config' => $this->configProvider->getConfig($position->getAsset()->getAssetType()),
        ]);
    }
}
