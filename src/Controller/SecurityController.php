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


class SecurityController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, UserRepository $userRepository)
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
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Authentication successful!',
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
