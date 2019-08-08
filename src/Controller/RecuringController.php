<?php

namespace App\Controller;

use App\Entity\Recuring;
use App\Repository\CategoriesRepository;
use App\Repository\RecuringRepository;
use App\Repository\TypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * Class CategoriesController
 * @package App\Controller
 *
 * @Route("/api/recurings")
 */
class RecuringController extends AbstractController {
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RecuringRepository
     */
    private $recuringRepository;

    /**
     * CategoriesController constructor.
     * @param EntityManagerInterface $entityManager
     * @param RecuringRepository $repository
     */
    public function __construct(EntityManagerInterface $entityManager, RecuringRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->recuringRepository = $repository;
    }

    /**
     * @FOSRest\Get("")
     *
     * @return View
     */
    public function getAll() {
        $recurings = $this->recuringRepository->findAll();

        return View::create($recurings, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/{id}")
     *
     * @param $id integer
     * @return View
     */
    public function get($id) {
        $recuring = $this->recuringRepository->find($id);

        return View::create($recuring, Response::HTTP_OK);
    }

    private function buildandSaveEntity($entity, $data, CategoriesRepository $catRepo, TypesRepository $typRepo) {
        $entity->setName($data->name);
        $entity->setType($typRepo->find($data->typeId));
        $entity->setCategory($catRepo->find($data->categoryId));
        $entity->setValue($data->value);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Create Category
     * @FOSRest\Post("")
     *
     * @param $request Request
     * @param $catRepo CategoriesRepository
     * @param $typRepo TypesRepository
     * @return View
     */
    public function add(Request $request, CategoriesRepository $catRepo, TypesRepository $typRepo) {
        $recuringMod = json_decode($request->getContent());
        $recuring =  new Recuring();

        $this->buildandSaveEntity($recuring, $recuringMod, $catRepo, $typRepo);

        return View::create($recuring, Response::HTTP_OK);
    }

    /**
     * Delete category
     * @FOSRest\Delete("/{id}")
     *
     * @param $id string
     * @return View
     */
    public function delete($id) {
        $recuring = $this->recuringRepository->find($id);

        if($recuring !== null) {
            $this->entityManager->remove($recuring);
            $this->entityManager->flush();

            return View::create($recuring, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Edit category
     * @FOSRest\Put("/{id}")
     *
     * @param $request Request
     * @param $catRepo CategoriesRepository
     * @param $typRepo TypesRepository
     * @param $id string
     * @return View
     */
    public function update(Request $request, CategoriesRepository $catRepo, TypesRepository $typRepo, $id) {
        $recuringMod = json_decode($request->getContent());
        $recuring = $this->recuringRepository->find($id);

        if($recuring !== null) {
            $this->buildandSaveEntity($recuring, $recuringMod, $catRepo, $typRepo);

            return View::create($recuring, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }


}
