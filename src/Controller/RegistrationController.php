<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Get the JSON payload from the request body
        $data = json_decode($request->getContent(), true);

        // Create a new user entity
        $user = new User();
        $user->setUsername($data['username']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);

        // Encode the password and set it on the user entity
        $password = $userPasswordHasher->hashPassword($user, $data['password']);
        $user->setPassword($password);

        // Persist the user entity to the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Return a success JSON response
        return $this->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
            ],
        ]);
    }

    /*
    #[Route('/register', name: 'app_register')]
public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
{
    $user = new User();
    $user->setUsername('');
    $user->setFirstName('');
    $user->setLastName('');

    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // encode the plain password
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $user->setUsername($form->get('username')->getData());
        $user->setFirstName($form->get('first_name')->getData());
        $user->setLastName($form->get('last_name')->getData());

        $entityManager->persist($user);
        $entityManager->flush();
        // do anything else you need here, like send an email
        return $this->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
             
            ],
        ]);

       // return $this->redirectToRoute('_profiler_home');  
    }
    

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}*/
}
