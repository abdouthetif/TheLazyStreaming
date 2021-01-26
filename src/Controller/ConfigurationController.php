<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ConfigurationController extends AbstractController
{
    #[Route('/user/config', name: 'user.config.index')]
    public function index(Request $request,
                           UserPasswordEncoderInterface $encoder,
                           EntityManagerInterface $manager
                           ): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $encoder->encodePassword($user, $plainPassword);
            $user->setPassWord($hashedPassword);


            $manager->flush();

            $this->addFlash('success', 'Votre à été bien configuré.');

            return $this->redirectToRoute('home.index');

        }

        return $this->render('configuration/index.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
