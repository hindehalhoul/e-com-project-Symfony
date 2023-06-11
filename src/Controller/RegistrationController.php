<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;


class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        
        $data = json_decode($request->getContent(), true);

        // Create a new user entity
        $user = new User();
        $user->setUsername($data['username']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);

        
        $password = $userPasswordHasher->hashPassword($user, $data['password']);
        $user->setPassword($password);

        // Enregistration des infor dans base de données 
        $entityManager->persist($user);
        $entityManager->flush();
        $response = $this->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
            ],
        ]);

        $response->headers->setCookie(new Cookie('user_id', $user->getId(), strtotime('+1 year')));
        
        return $response;
    }
}
