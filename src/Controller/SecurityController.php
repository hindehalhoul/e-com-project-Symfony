<?php

namespace App\Controller;


use App\Repository\UserRepository;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Cookie;




class SecurityController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, SessionInterface $session, UserRepository $userRepository): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['email' => $data['email']]);
        if (!$user) {
            return new JsonResponse([
                'status' => 'not found',
                'message' => 'User not found',
            ]);
        }
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'data' => []
            ]);
        }
        $session->set('user_id', $user->getId());
        $response = new JsonResponse([
            'status' => 'success',
            'message' => 'Authentication successful!',
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'session_id' => $session->get('user_id')
            ]
        ]);
        $response->headers->setCookie(new Cookie('user_id', $user->getId(), strtotime('+1 year')));

        return $response;
    }


    // logout doesnt work with postman, it needs the browser


    // #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    // public function logout(): JsonResponse
    // {
    //     $request = Request::createFromGlobals();
    //     $response = new JsonResponse();
    //     $response->headers->clearCookie('user_id');
    //     $response->headers->setCookie(
    //         Cookie::create('user_id', null, time() - 3600)
    //     );
    //     $response->send();
    //     return $response;
    // }
}
