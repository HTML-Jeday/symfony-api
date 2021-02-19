<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="products-all", methods={"POST"})
     */
    public function showProducts(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->json(
            $products,
            Response::HTTP_OK
        );
    }
    /**
     * @Route("/api/product", name="product-create", methods={"POST"})
     */
    public function createProduct(Request $request): Response
    {      
        $link = $request->get('link');
        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($link);

        if(!$category){
            return $this->json(['message' => 'category does not exist']);
        }

        $name = $request->get('name');
        $description = $request->get('description');
        $price = $request->get('price');
        $quantity = $request->get('quantity');

        if(!$name | !$description | !$price | !$quantity){
            return $this->json(['message' => 'product details cannot be empty']);
        }

        $product = new Product();
        $product->setLink($category);
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setQuantity($quantity);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        
        return $this->json($product, Response::HTTP_ACCEPTED);

    }
    /**
     * @Route("/api/product/{id}", name="product-delete", methods={"DELETE"})
     */
    public function deleteProduct($id): Response
    {
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);
        if(!$product){
            return $this->json(
                ["message" => "product with id :$id have not been found"],
                Response::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->json(
            ["message" => "product with id: $id has been deleted"],
            Response::HTTP_OK
        );
    }
     /**
     * @Route("/api/product/{id}", name="product-update", methods={"PUT"})
     */
    public function updateProduct($id , Request $request): Response
    {
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);
        if(!$product){
            return $this->json(
                ["message" => "product with id :$id have not been found"],
                Response::HTTP_NOT_FOUND
            );
        }
    
        $name = $request->get('name') ?? $product->getName();
        $description = $request->get('description') ?? $product->getDescription();
        $price = $request->get('price') ?? $product->getPrice();
        $quantity = $request->get('quantity') ?? $product->getQuantity();
        $link = $request->get('link') ?? $product->getLink();

        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($link);
        if(!$category){
            return $this->json(
                ["message" => "category with id :$id have not been found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $product
        ->setName($name)
        ->setDescription($description)
        ->setPrice($price)
        ->setQuantity($quantity)
        ->setLink($link);
        

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(
            ["message" => "product with id: $id has been updated"],
            Response::HTTP_OK
        );
    }
      /**
     * @Route("/api/product/{id}", name="product-find", methods={"POST"})
     */
    public function findProduct($id): Response
    {
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);
        if(!$product){
            return $this->json(
                ["message" => "product with id :$id have not been found"],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $product,
            Response::HTTP_OK
        );
    }
}
