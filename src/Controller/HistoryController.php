<?php

namespace App\Controller;

use App\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * Class HistoryController
 * @package App\Controller
 *
 * @Route("/api/history")
 */
class HistoryController extends  AbstractController {
    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    public function __construct(HistoryRepository $repository)
    {
        $this->historyRepository = $repository;
    }

    /**
     * @FosRest\Get("/{year<\d+>}/{month<\d+>}")
     * @param $year
     * @param $month
     *
     * @return View
     */
    public function getByYearAndMonth($year, $month) {
        $historyData = $this->historyRepository->findBy(["year" => $year, "month" => $month]);

        return View::create($historyData, Response::HTTP_OK);
    }

    /**
     * @FosRest\Get("/periods")
     *
     * @return View
     */
    public function getHistoryAvailablePeriods() {
        $periods = $this->historyRepository->getMonthsList();

        return View::create($periods, Response::HTTP_OK);
    }

    /**
     * @FosRest\Get("/years")
     *
     * @return View
     */
    public function getHistoryAvailableYears() {
        $years = $this->historyRepository->getYearsList();

        return View::create($years, Response::HTTP_OK);
    }

    /**
     * @FosRest\Get("/{year<\d+>}")
     * @param $year
     *
     * @return View
     */
    public function getByYear($year) {
        $historyData = $this->historyRepository->getChartHistoryData($year);

        return View::create($historyData, Response::HTTP_OK);
    }
}
