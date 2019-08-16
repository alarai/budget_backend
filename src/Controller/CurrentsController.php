<?php

namespace App\Controller;

use App\Entity\Currents;
use App\Repository\CurrentsRepository;
use App\Repository\CategoriesRepository;
use App\Repository\TypesRepository;
use App\Repository\RecuringRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * Class CurrentsController
 * @package App\Controller
 *
 * @Route("/api/currents")
 */
class CurrentsController extends AbstractController {
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CurrentsRepository
     */
    private $currentsRepository;

    /**
     * CurrentsController constructor.
     * @param EntityManagerInterface $entityManager
     * @param CurrentsRepository $repository
     */
    public function __construct(EntityManagerInterface $entityManager, CurrentsRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->currentsRepository = $repository;
    }

    /**
     * Get all currents
     * @FOSRest\Get("")
     *
     * @return View
     */
    public function getAll() {
        $currents = $this->currentsRepository->findAll();

        return View::create($currents, Response::HTTP_OK);
    }

    /**
     * Get a current details
     * @FOSRest\Get("/{id}")
     *
     * @param $id integer
     * @return View
     */
    public function get($id) {
        $currents = $this->currentsRepository->find($id);

        return View::create($currents, Response::HTTP_OK);
    }

    /**
     * Date formatter
     *
     * @param $str String to turn into date
     * @return bool|\DateTime
     */
    private function convertDate($str) {
        return \DateTime::createFromFormat("Y-m-d", $str);
    }

    /**
     * Save an entity
     *
     * @param Currents $entity
     * @param $data New data to set
     * @param CategoriesRepository $catRepo
     * @param TypesRepository $typRepo
     */
    private function buildandSaveEntity(Currents $entity, $data, CategoriesRepository $catRepo, TypesRepository $typRepo) {
        $entity->setName($data->name);
        $entity->setType($typRepo->find($data->typeId));
        $entity->setCategory($catRepo->find($data->categoryId));
        $entity->setValue($data->value);
        $entity->setDate($this->convertDate($data->date));
        $entity->setChecked($data->checked);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Create a current operation
     * @FOSRest\Post("")
     *
     * @param $request Request
     * @param $catRepo CategoriesRepository
     * @param $typRepo TypesRepository
     * @return View
     */
    public function add(Request $request, CategoriesRepository $catRepo, TypesRepository $typRepo) {

        $currentMod = json_decode($request->getContent());
        $current =  new Currents();

        $this->buildandSaveEntity($current, $currentMod, $catRepo, $typRepo);

        return View::create($current, Response::HTTP_CREATED);
    }

    /**
     * Delete a current operation
     * @FOSRest\Delete("/{id}")
     *
     * @param $id string
     * @return View
     */
    public function delete($id) {
        $category = $this->currentsRepository->find($id);

        if($category !== null) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();

            return View::create($category, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Edit a current operation
     * @FOSRest\Put("/{id}")
     *
     * @param $request Request
     * @param $catRepo CategoriesRepository
     * @param $typRepo TypesRepository
     * @param $id string
     * @return View
     */
    public function update(Request $request, CategoriesRepository $catRepo, TypesRepository $typRepo, $id) {
        $currentMod = json_decode($request->getContent());
        $current = $this->currentsRepository->find($id);

        if($current !== null) {
            $this->buildandSaveEntity($current, $currentMod, $catRepo, $typRepo);

            return View::create($current, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);

    }

    /**
     * Add a recuring operation to current operation
     *
     * @param $recurings RecuringRepository
     * @param $id string
     *
     * @return View
     *
     * @FOSRest\Get("/addrecur/{id<\d+>}", name="current_addrecur")
     */
    public function addRecuringOperation(RecuringRepository $recurings, $id)
    {
        $recuring = $recurings->find($id);
        if ($recuring == null) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        $itemExist = $this->currentsRepository->findBy(["recuring" => $recuring]);
        if ($itemExist) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        $operation = new Currents();

        $operation->setName($recuring->getName());
        $operation->setChecked(true);
        $operation->setCategory($recuring->getCategory());
        $operation->setType($recuring->getType());
        $operation->setValue($recuring->getValue());
        $operation->setRecuring($recuring);

        $this->entityManager->persist($operation);
        $this->entityManager->flush();


        return View::create($operation, Response::HTTP_OK);
    }


    /**
     * Change a current operation status
     *
     * @param $id string
     * @return View
     *
     * @FOSRest\Get("/check/{id<\d+>}", name="current_checked")
     */
    public function checkOperation($id)
    {
        $operation = $this->currentsRepository->find($id);

        if ($operation == null) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        $operation->setChecked(!$operation->getChecked());

        $this->entityManager->persist($operation);
        $this->entityManager->flush();


        return View::create($operation, Response::HTTP_OK);
    }





    /**
     * Historize all checked current operation
     *
     * @param $request Request
     * @param $categoriesRepository CategoriesRepository
     * @param TypesRepository $typesRepository
     *
     * @return View
     * @throws
     *
     *  @FOSRest\Post("/historize", name="current_historize")
     */
    public function historize(Request $request, CategoriesRepository $categoriesRepository, TypesRepository $typesRepository)
    {
        $data = json_decode($request->getContent());

        if (!$data || !preg_match('/^20[0-9]{2}$/', $data->year) || !preg_match('/^1[0-2]$|^0?[1-9]$/', $data->month)) {
            return View::create(null, Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->beginTransaction();
        try {
            $remain = $this->currentsRepository->getRemainingPassed()["value"];

            $this->currentsRepository->historizeData($data->month, $data->year);
            $this->currentsRepository->removeAllPassedOperations();

            $category = $categoriesRepository->findOneBy(["useForHistory" => true] );
            $type = $typesRepository->findOneBy(["useForHistory" => true] );

            if($category === null ) {
                $this->entityManager->rollback();
                return View::create("No Category was marked for historization", Response::HTTP_FAILED_DEPENDENCY);
            }

            if($type === null ) {
                $this->entityManager->rollback();
                return View::create("No Type was marked for historization", Response::HTTP_FAILED_DEPENDENCY);
            }

            $current = new Currents();
            $current->setDate(new \DateTime());
            $current->setValue($remain);
            $current->setType($type);
            $current->setCategory($category);
            $current->setChecked(true);
            $current->setName("Previous month balance");

            $this->entityManager->persist($current);
            $this->entityManager->flush();

            $this->entityManager->commit();
        }catch (Exception $e) {
            $this->entityManager->rollback();
        }

        return View::create(null, Response::HTTP_OK);
    }

}
