<?php

namespace App\Controller;

use App\Entity\Liste;
use App\Form\UserType;
use App\Repository\ListeRepository;
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

    #[Route('/user/liste/add', name: 'user.addListe')]
    public function addListe(EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!isset($_GET) || !array_key_exists('id', $_GET) || !array_key_exists('type', $_GET) || !array_key_exists('liste', $_GET)) {
            echo "Erreur l'id du film ou de la série n'existe pas";
            exit;
        }

        $liste = new Liste();
        $liste->setUser($this->getUser());
        $liste->setName($_GET['liste']);

        if ($_GET['type'] == 'movie') {
            $liste->setIdMovie($_GET['id']);
        }
        else {
            $liste->setIdSerie($_GET['id']);
        }

        $manager->persist($liste);
        $manager->flush();

        $this->addFlash('success', 'Ce film/série a bien été ajouté à votre liste');

        // Redirection vers la page du film
        return $this->redirectToRoute('search.detailsDisplay', ['type' => $_GET['type'], 'id' => $_GET['id']]);
    }

    #[Route('/user/liste/remove', name: 'user.removeListe')]
    public function removeListe(ListeRepository $listeRepository, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!isset($_GET) || !array_key_exists('id', $_GET) || !array_key_exists('type', $_GET) || !array_key_exists('liste', $_GET)) {
            echo "Erreur l'id du film ou de la série n'existe pas";
            exit;
        }

        $criteria = [
            'name' => $_GET['liste']
        ];

        if ($_GET['type'] == 'movie') {
            $criteria += ['id_movie' => $_GET['id']];
        }
        else {
            $criteria += ['id_movie' => $_GET['id']];
        }

        $liste = $listeRepository->findOneBy($criteria);
        $user = $this->getUser()->removeListe($liste);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'Ce film/série a bien été supprimé de votre liste');

        // Redirection vers la page du film
        return $this->redirectToRoute('search.detailsDisplay', ['type' => $_GET['type'], 'id' => $_GET['id']]);
    }
}
