<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    // #[Route('/{id}', name: 'prod_details', methods: ['GET'])]
    // public function showProdDetails(ProductRepository $productRepository, int $id): JsonResponse
    // {
    //     $product = $productRepository->find($id);
    //     $data = [];

    //     if (!$product) {
    //         return new JsonResponse([
    //             'status' => '404',
    //             'message' => 'Product not found',
    //         ]);
    //     }
    //     return new JsonResponse([
    //         'status' => 'success',
    //         'message' => 'Product fetched successfully',
    //         'product' => [
    //             'id' => $product->getId(),
    //             'name' => $product->getNom(),
    //             'price (Dhs)' => $product->getPrix(),
    //             'description' => $product->getDescription(),
    //             'image' => $product->getImage(),
    //             'add_to_cart_url' => $this->generateUrl('add_to_cart', ['id' => $product->getId()]),
    //         ]
    //     ]);
    // }


    // #[Route('/{id}/add-to-cart', name: 'add_to_cart', methods: ['POST'])]
    // public function addToCart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, int $id): JsonResponse
    // {
    //     $cookieRequest = Request::createFromGlobals();

    //     if ($request->cookies->has('user_id')) {
    //         $product = $productRepository->find($id);
    //         $userId = $cookieRequest->cookies->get('user_id');
    //         $user = $entityManager->getRepository(User::class)->find($userId);
    //         $cart = $entityManager->getRepository(Cart::class)->findOneBy([
    //             'user_id' => $userId,
    //             'product_id' => $id,
    //         ]);
    //         if (!$product) {
    //             return new JsonResponse([
    //                 'status' => '404',
    //                 'message' => 'Product not found',
    //             ]);
    //         }
    //         if (!$cart) {
    //             $cart = new Cart();
    //             $cart->setUserId($userId);
    //             $cart->setProductId($id);
    //             $cart->setQuantity(1);
    //             $cart->setPrice($product->getPrix());
    //         } else {
    //             if (!isset($cart)) {
    //                 $cart = [
    //                     'product' => $product,
    //                     'quantity' => 1,
    //                 ];
    //             } else {
    //                 $cart->setQuantity($cart->getQuantity() + 1);;
    //             }
    //         }
    //         $entityManager->persist($cart);
    //         $entityManager->flush();

    //         return new JsonResponse([
    //             'status' => 'success',
    //             'message' => 'Product added to cart',
    //         ]);
    //     } else {
    //         return new JsonResponse([
    //             'status' => 'Failed',
    //             'message' => 'Not conneted',
    //             'login_url' => $this->generateUrl('app_login'),
    //         ]);
    //     }
    // }
}
