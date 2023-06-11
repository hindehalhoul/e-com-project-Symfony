<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('base.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/product/{id}', name: 'prod_details', methods: ['POST', 'GET'])]
    public function showProdDetails(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('product/details.html.twig', [
            'product' => $product,
        ]);
    }


    #[Route('/{id}/add-to-cart', name: 'add_to_cart', methods: ['POST', 'GET'])]
    public function addToCart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, int $id): Response
    {
        $cookieRequest = Request::createFromGlobals();

        if ($request->cookies->has('user_id')) {
            $product = $productRepository->find($id);
            $userId = $cookieRequest->cookies->get('user_id');
            $user = $entityManager->getRepository(User::class)->find($userId);
            $cart = $entityManager->getRepository(Cart::class)->findOneBy([
                'user_id' => $userId,
                'product_id' => $id,
            ]);
            if (!$product) {
                return new JsonResponse([
                    'status' => '404',
                    'message' => 'Product not found',
                ]);
            }
            if (!$cart) {
                $cart = new Cart();
                $cart->setUserId($userId);
                $cart->setProductId($id);
                $cart->setQuantity(1);
                $cart->setPrice($product->getPrix());
            } else {
                if (!isset($cart)) {
                    $cart = [
                        'product' => $product,
                        'quantity' => 1,
                    ];
                } else {
                    $cart->setQuantity($cart->getQuantity() + 1);;
                }
            }
            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('cart');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
