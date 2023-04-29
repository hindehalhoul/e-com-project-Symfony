<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Cart;
use App\Entity\User;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cookieRequest = Request::createFromGlobals();

        // $cart = $request->getSession()->get('cart', []);
        // $data = [];
        $userId = $cookieRequest->cookies->has('user_id');
        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'user_id' => $userId,
        ]);

        // Check if a cookie exists
        if ($cookieRequest->cookies->has('user_id')) {
            // $cookieValue = $cookieRequest->cookies->get('user_id');
            foreach ($cart as $id => $item) {
                $data[] = [
                    'id' => $item['product']->getId(),
                    'name' => $item['product']->getNom(),
                    'price (Dhs)' => $item['product']->getPrix(),
                    'quantity' => $item['quantity'],
                ];
            }
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Cart fetched successfully',
                'data' => $data
            ]);
        } else {
            return new JsonResponse([
                'status' => 'Failed',
                'message' => 'Not conneted',
                'login_url' => $this->generateUrl('app_login'),
            ]);
        }
    }
}


// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;

// use Symfony\Component\HttpFoundation\JsonResponse;

// class CartController extends AbstractController
// {
//     #[Route('/cart', name: 'app_cart', methods: ['GET'])]

//     public function showCart(SessionInterface $session): JsonResponse
// {
//     $cart = $session->get('cart', []);
//     $cartItems = [];

//     foreach ($cart as $productId => $productData) {
//         $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

//         if ($product) {
//             $cartItems[] = [
//                 'product' => $product,
//                 'quantity' => $productData['quantity'],
//                 'total_price' => $productData['quantity'] * $product->getPrice(),
//             ];
//         }
//     }

//     return $this->json([
//         'cartItems' => $cartItems,
//     ]);
// }
//     public function index(): Response
//     {
//         $session = $this->getRequest()->getSession();
//         $cart = $session->get('cart', []);

//         $cartItems = [];

// foreach ($cart as $productId => $productData) {
//     $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

//     if ($product) {
//         $cartItems[] = [
//             'product' => $product,
//             'quantity' => $productData['quantity'],
//             'total_price' => $productData['quantity'] * $product->getPrice(),
//         ];
//     }
// }


// return $this->json([
//     'cartItems' => $cartItems,
// ]);

//     }
// }
