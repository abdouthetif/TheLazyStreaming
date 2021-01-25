<?php

namespace App\Controller;

use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{
    #[Route('/signup', name: 'user.signup')]
    public function signup(Request $request,
                           UserPasswordEncoderInterface $encoder,
                           EntityManagerInterface $manager,
                           GuardAuthenticatorHandler $authenticatorHandler,
                           LoginFormAuthenticator $authenticator): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $encoder->encodePassword($user, $plainPassword);
            $user->setPassWord($hashedPassword);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte est créé.');

            return $authenticatorHandler->authenticateUserAndHandleSuccess(
                $user, $request, $authenticator, 'main'
            );
        }

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
