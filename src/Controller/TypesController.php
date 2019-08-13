<?php

namespace App\Controller;

use App\Repository\TypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * Class TypesController
 * @package App\Controller
 *
 * @Route("/api/types")
 */
class TypesController extends AbstractController {
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var typesRepository
     */
    private $typesRepository;

    /**
     * CategoriesController constructor.
     * @param EntityManagerInterface $entityManager
     * @param typesRepository $repository
     */
    public function __construct(EntityManagerInterface $entityManager, TypesRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->typesRepository = $repository;
    }

    /**
     * @FOSRest\Get("")
     *
     * @return View
     */
    public function getAll() {
        $types = $this->typesRepository->findAll();

        return View::create($types, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/{id}")
     *
     * @param $id integer
     * @return View
     */
    public function get($id) {
        $type = $this->typesRepository->find($id);

        if($type===null) {
            return View::create("The Type could not be found", Response::HTTP_NOT_FOUND);
        }

        return View::create($type, Response::HTTP_OK);
    }

    /**
     * Create Category
     * @FOSRest\Post("")
     *
     * @param $request Request
     * @param $serializer SerializerInterface
     * @return View
     */
    public function add(Request $request, SerializerInterface $serializer) {
        $category = $serializer->deserialize($request->getContent(), 'App\Entity\Types', 'json');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return View::create($category, Response::HTTP_CREATED, []);
    }

    /**
     * Delete category
     * @FOSRest\Delete("/{id}")
     *
     * @param $id string
     * @return View
     */
    public function delete($id) {
        $type = $this->typesRepository->find($id);

        if($type !== null) {
            $this->entityManager->remove($type);
            $this->entityManager->flush();

            return View::create($type, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Edit category
     * @FOSRest\Put("/{id}")
     *
     * @param $request Request
     * @param $serializer SerializerInterface
     * @param $id string
     * @return View
     */
    public function update(Request $request, SerializerInterface $serializer, $id) {
        $typeMod = $serializer->deserialize($request->getContent(), 'App\Entity\Types', 'json');

        $type = $this->typesRepository->find($id);

        if($type !== null) {
            $type->setName($typeMod->getName());
            $this->entityManager->persist($type);
            $this->entityManager->flush();

            return View::create($type, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }


}
