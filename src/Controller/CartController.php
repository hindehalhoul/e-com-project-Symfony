<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $cart = $request->getSession()->get('cart', []);
        $data = [];

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
    }
}
#[Route('/cart/delete/{id}', name: 'cart_delete', methods: ['DELETE'])]
public function delete(Request $request, int $id): JsonResponse
{
    $cart = $request->cookies->get('cart', '{}');
    $cart = json_decode($cart, true);

    if (isset($cart[$id])) {
        unset($cart[$id]);
        $cart = json_encode($cart);

        // Create a new cookie with the updated cart data and the same expiration time
        $response = new JsonResponse([
            'status' => 'success',
            'message' => 'Product removed from cart successfully'
        ]);
        $response->headers->setCookie(new Cookie('cart', $cart, strtotime('+1 year')));

        return $response;
    } else {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Product not found in cart'
        ], 404);
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
