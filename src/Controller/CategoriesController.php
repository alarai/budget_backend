<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * Class CategoriesController
 * @package App\Controller
 *
 * @Route("/api")
 */
class CategoriesController extends AbstractController {
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CategoriesRepository
     */
    private $categoriesRepository;

    /**
     * CategoriesController constructor.
     * @param EntityManagerInterface $entityManager
     * @param CategoriesRepository $repository
     */
    public function __construct(EntityManagerInterface $entityManager, CategoriesRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->categoriesRepository = $repository;
    }

    /**
     * @FOSRest\Get("/categories")
     *
     * @return View
     */
    public function getAll() {
        $categories = $this->categoriesRepository->findAll();

        return View::create($categories, Response::HTTP_OK);
    }

    /**
     * Create Category
     * @FOSRest\Post("/category")
     *
     * @param $request Request
     * @return View
     */
    public function add(Request $request) {
        $category = new Categories();
        $category->setName($request->get('name'));

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return View::create($category, Response::HTTP_CREATED, []);
    }

    /**
     * Delete category
     * @FOSRest\Delete("/category/{id}")
     *
     * @param $id string
     * @return View
     */
    public function delete($id) {
        $category = $this->categoriesRepository->find($id);

        if($category !== null) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();

            return View::create($category, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Edit category
     * @FOSRest\Put("/category/{id}")
     *
     * @param $request Request
     * @param $serializer SerializerInterface
     * @param $id string
     * @return View
     */
    public function update(Request $request, SerializerInterface $serializer, $id) {
        $categoryMod = $serializer->deserialize($request->getContent(), 'App\Entity\Categories', 'json');

        $category = $this->categoriesRepository->find($id);

        if($category !== null) {
            $category->setName($categoryMod->getName());
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return View::create($category, Response::HTTP_OK);
        }

        return View::create(null, Response::HTTP_NOT_FOUND);
    }


}
