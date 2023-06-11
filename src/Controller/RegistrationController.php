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

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // $data = json_decode($request->getContent(), true);

        // $user = new User();
        // $user->setUsername($data['username']);
        // $user->setFirstName($data['first_name']);
        // $user->setLastName($data['last_name']);
        // $user->setEmail($data['email']);

        // $password = $userPasswordHasher->hashPassword($user, $data['password']);
        // $user->setPassword($password);

        // $entityManager->persist($user);
        // $entityManager->flush();
        // $response = $this->json([
        //     'status' => 'success',
        //     'message' => 'User registered successfully',
        //     'user' => [
        //         'username' => $user->getUsername(),
        //         'email' => $user->getEmail(),
        //         'first_name' => $user->getFirstName(),
        //         'last_name' => $user->getLastName(),
        //     ],
        // ]);

        // $response->headers->setCookie(new Cookie('user_id', $user->getId(), strtotime('+1 year')));
        // // return $response;
        if ($request->isMethod('POST')) {
            if ($request->isMethod('POST')) {
                $username = $request->request->get('username');
                $firstName = $request->request->get('first_name');
                $lastName = $request->request->get('last_name');
                $email = $request->request->get('email');
                $password = $request->request->get('password');

                $user = new User();
                $user->setUsername($username);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setEmail($email);

                $hashedPassword = $userPasswordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                echo '<script>alert("Account created ! Please Login ")</script>';

                $response = $this->render('security/login.html.twig', [
                    'user' => $user
                ]);
                return $response;
            }
        }
        return $this->render('registration/register.html.twig');
    }
}
