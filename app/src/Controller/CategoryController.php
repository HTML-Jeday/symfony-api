<?php

namespace App\Controller;


use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class CategoryController extends AbstractController
{
    /**
     * @Route("/api/categories", name="all-categories", methods={"POST"})
     */
    public function showCategories(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->json(
            $categories,
            Response::HTTP_OK);
    }
    /**
     * @Route("/api/category", name="category-create", methods={"POST"})
     */
    public function createCategory(Request $request, SerializerInterface $serializer): Response
    {
        $name = $request->get('name');
          if(strlen($name) < 3 || is_null($name)){
            return $this->json(
                ["message" => "name must be at least 3 characters"],
                Response::HTTP_BAD_REQUEST
            );
        }
        
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $this->json(
            $category,
            Response::HTTP_CREATED);
    }
    /**
     * @Route("/api/category/{id}", name="category-delete", methods={"DELETE"})
     */
    public function delteCategory($id): Response
    {
        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        if(!$category){
            return $this->json(
                ["message" => "category with id :$id have not been found"],
                Response::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->json(
            ["message" => "category with id: $id has been deleted"],
            Response::HTTP_OK
        );
    }
    /**
     * @Route("/api/category/{id}", name="category-find", methods={"POST"})
     */
    public function findCategory($id): Response
    {
        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        if(!$category){
            return $this->json(
                ["message" => "category with id :$id have not been found"],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $category,
            Response::HTTP_OK
        );
    }
     /**
     * @Route("/api/category/{id}", name="category-update", methods={"PUT"})
     */
    public function updateCategory($id, Request $request): Response
    {
        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        if(!$category){
            return $this->json(
                ["message" => "category with id :$id have not been found"],
                Response::HTTP_NOT_FOUND
            );
        }
              
        $name = $request->get('name') ?? $category->getName();
        $category->setName($name);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $this->json(
            ["message" => "category with id: $id has been updated"],
            Response::HTTP_OK
        );
    }
}
