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
                'image' => $product->getImage()
            ]
        ]);
    }
}
