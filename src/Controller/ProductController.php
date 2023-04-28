<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getNom(),
                'price (Dhs)' => $product->getPrix()
            ];
        }
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Products fetched successfully',
            'data' => $data
        ]);
    }

    #[Route('/{id}', name: 'prod_details', methods: ['GET'])]
    public function showProdDetails(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->find($id);
        $data = [];

        if (!$product) {
            return new JsonResponse([
                'status' => '404',
                'message' => 'Product not found',
            ]);
        }
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Product fetched successfully',
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getNom(),
                'price (Dhs)' => $product->getPrix(),
                'description' => $product->getDescription(),
                'image' => $product->getImage(),
                'add_to_cart_url' => $this->generateUrl('add_to_cart', ['id' => $product->getId()]),
            ]
        ]);
    }
    #[Route('/{id}/add-to-cart', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request, ProductRepository $productRepository, int $id): JsonResponse
    {
        // Retrieve the product from the database
        $product = $productRepository->find($id);

        // Check if the product exists
        if (!$product) {
            return new JsonResponse([
                'status' => '404',
                'message' => 'Product not found',
            ]);
        }

        // Add the product to the cart
        $cart = $request->getSession()->get('cart', []);
        if (!isset($cart[$id])) {
            $cart[$id] = [
                'product' => $product,
                'quantity' => 1,
            ];
        } else {
            $cart[$id]['quantity']++;
        }
        $request->getSession()->set('cart', $cart);

        // Return a success response
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Product added to cart',
        ]);
    }
}
