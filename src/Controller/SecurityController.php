<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;



class SecurityController extends AbstractController
{
    public $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user) {
                echo '<script>alert("Invalid email or password !")</script>';
            } else {
                if ($this->passwordHasher->isPasswordValid($user, $password)) {
                    $response = $this->render('home', [
                        'user' => $user
                    ]);
                    $response->headers->setCookie(new Cookie('user_id', $user->getId(), strtotime('+1 year')));
                    return $response;
                } else {
                    echo '<script>alert("Invalid email or password !")</script>';
                }
            }
        }
        return $this->render('security/login.html.twig');
    }


    #[Route(path: '/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout(): Response
    {
        echo '<script>alert("logout 1")</script>';
        $response = new Response();
        echo '<script>alert("logout 2")</script>';
        $response->headers->clearCookie('user_id');
        echo '<script>alert("logout 3")</script>';
        $response->headers->setCookie(
            Cookie::create('user_id', null, time() - 3600)
        );
        echo '<script>alert("logout 4")</script>';
        $response->send();
        echo '<script>alert("logout 5")</script>';
        $this->redirectToRoute('app_login');
        echo '<script>alert("logout 6")</script>';

        return $response;
    }
}
