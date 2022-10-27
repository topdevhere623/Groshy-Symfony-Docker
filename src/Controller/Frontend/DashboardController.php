<?php

namespace Groshy\Controller\Frontend;

use DateTime;
use Groshy\Provider\DashboardProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\Component\Resource\Repository\RepositoryInterface;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly DashboardProvider $provider,
        private readonly RepositoryInterface $positionRepository,
        private readonly RepositoryInterface $assetTypeRepository,
        private readonly RepositoryInterface $transactionRepository,
        private readonly RepositoryInterface $positionValueRepository,
    ) {
    }

    /**
     * @Route("/user/dashboard")
     */
    public function dashboardAction(Request $request): Response
    {
        $to = DateTime::createFromFormat('Y-m-d', $request->query->get('to'));
        $from = DateTime::createFromFormat('Y-m-d', $request->query->get('from'));
        $mainType = $this->assetTypeRepository->getDashboardType();

        return $this->render('dashboard/dashboard.html.twig', [
            'from' => $from,
            'to' => $to,
            'dash' => $this->provider->getDashboardData($from, $to, $this->getUser()),
            'maxDate' => $this->positionValueRepository->getFirstDate($this->getUser()),
            'positions' => $this->positionRepository->byType($mainType, $this->getUser()),
            'transactions' => $this->transactionRepository->byType($mainType, $this->getUser()),
            'sponsors' => $this->positionRepository->groupBySponsor($this->getUser()),
            'years' => $this->positionRepository->groupByYear($this->getUser()),
        ]);
    }
}
