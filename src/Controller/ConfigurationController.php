<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConfigurationController extends AbstractController
{
    #[Route('/user/config', name: 'user.config.index')]
    public function index(Request $request,
                          UserPasswordEncoderInterface $encoder,
                          EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $encoder->encodePassword($user, $plainPassword);
            $user->setPassWord($hashedPassword);

            $manager->flush();

            $this->addFlash('success', 'Votre compte a bien été configuré.');

            return $this->redirectToRoute('home.index');
        }

        return $this->render('configuration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/delete', name: 'user.delete')]
    public function delete(EntityManagerInterface $manager, UserRepository $userRepository): Response
    {
        if (!isset($_GET) || !array_key_exists('id', $_GET)) {
            echo "l'id est invalide";
            exit;
        }

        $userSess = $this->getUser();

        $user = $userRepository->findOneBy(['id' => intval($_GET['id'])]);

        if ($userSess->getUsername() == $user->getEmail()) {

            $session = new Session();
            $session->invalidate();

            $manager->remove($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte a bien été supprimé.');

            return $this->redirectToRoute('security.logout');
        }
    }
}
