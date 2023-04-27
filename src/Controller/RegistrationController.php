<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;



class RegistrationController extends AbstractController
{
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
}

}
