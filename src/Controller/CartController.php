<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Cart;
use App\Entity\Product;


class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart', methods: ['POST', 'GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $cookieRequest = Request::createFromGlobals();
        $total = 0;

        if ($cookieRequest->cookies->has('user_id')) {
            $userId = $cookieRequest->cookies->get('user_id');
            $carts = $entityManager->getRepository(Cart::class)->findBy([
                'user_id' => $userId,
            ]);
            $cartItems = [];
            $productData = [];
            foreach ($carts as $cart) {
                $productId = $cart->getProductId();
                $product = $entityManager->getRepository(Product::class)->find($productId);
                $productData = [
                    'id' => $product->getId(),
                    'name' => $product->getNom(),
                    'price' => $product->getPrix(),
                    'image' => $product->getImage(),
                ];
                $cartItems[] = [
                    'id' => $cart->getId(),
                    'product' => $productData,
                    'quantity' => $cart->getQuantity(),
                    'price' => $cart->getPrice(),
                ];
                $qte = $cart->getQuantity();
                $price = $cart->getPrice();
                $total += ($price * $qte);
            }
            return $this->render('cart/cart.html.twig', [
                'cart' => $cartItems,
                'total' => $total,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    // public function updateQte(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $cart = $entityManager->getRepository(Cart::class)->find($id);

    //     if (!$cart) {
    //         return new JsonResponse([
    //             'status' => 'Failed',
    //             'message' => 'Cart not found',
    //         ]);
    //     }
    //     $quantity = $data['quantity'];
    //     $cart->setQuantity($quantity);
    //     $entityManager->persist($cart);
    //     $entityManager->flush();

    //     return new JsonResponse([
    //         'status' => 'Success',
    //         'message' => 'Cart quantity updated !',
    //         'view cart' => $this->generateUrl('cart'),
    //     ]);
    // }

    // #[Route('/cart/delete/{id}', name: 'cart_delete', methods: ['DELETE'])]
    // public function deleteProduct(EntityManagerInterface $entityManager, int $id): JsonResponse
    // {
    //     $cart = $entityManager->getRepository(Cart::class)->find($id);

    //     if (!$cart) {
    //         return new JsonResponse([
    //             'status' => 'Failed',
    //             'message' => 'Product not found',
    //         ]);
    //     }

    //     $entityManager->remove($cart);
    //     $entityManager->flush();

    //     return new JsonResponse([
    //         'status' => 'Success',
    //         'message' => 'Product deleted from the cart',
    //         'view cart' => $this->generateUrl('cart'),
    //     ]);
    // }
}
