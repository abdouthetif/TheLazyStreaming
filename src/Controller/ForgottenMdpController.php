<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConfirmCodeType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ForgottenMdpType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ForgottenMdpController extends AbstractController
{
    /**
     * @Route("/login/password_forgotten", name="password_forgotten")]
     * @param Request $request
     * @param MailerInterface $mailer
     * @param $userRepository
     * @return Response
     * @throws TransportExceptionInterface
     * @method User|null findOneBy(array $criteria, array $orderBy = null,)
     */


    public function reinitializeMdp(Request $request, MailerInterface $mailer, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(ForgottenMdpType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);


            if (!empty($user)) {

                $i = 0; //counter
                $pin = ""; //our default pin is blank.
                while ($i < 4) {
                    //generate a random number between 0 and 9.
                    $pin .= rand(0, 9);
                    $i++;
                }

                //générer code à 4 chiffres
                $user->setCode($pin); //enregistrer le code dans l'entité user
                $manager->persist($user);
                $manager->flush();

                $request->getSession()->set('email', $email);

                $mail = (new TemplatedEmail())
                    ->from('destinataire@demo.test')
                    ->to($email = $form->get('email')->getData())
                    ->subject('Reinitialiser votre mot de passe')
                    ->html('<h1>Bonjour,</h1><br>
                                  <p>Il parrait que vous auriez oublier votre mot de passe, ne vous inquiétez pas nous nous occupons de ça !<br>
                                      Copier ce code dans le champ "Code" à la redirection de la page "Mot de passe oublié" : </p>' . $pin);

                $mailer->send($mail);

                $this->addFlash('success', 'Votre demande a bien été enregistré.');
                return $this->redirectToRoute('password_forgotten/confirm_code');
            }
            else {

                $this->addFlash('denied', 'L\'e-mail indiqué est invalide.');
                return $this->redirect($request->headers->get('referer'));
            }

        }

        return $this->render('forgotten_mdp/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/login/password_forgotten/confirm_code", name="password_forgotten/confirm_code")]
    */
    public function confirmCode(Request $request, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, UserRepository $userRepository) : Response
    {

        $form = $this->createForm(ConfirmCodeType::class);
		$form->handleRequest($request);
		
        $email = $request->getSession()->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!empty($user)) {

            if ($form->isSubmitted() && $form->isValid()) {

                if ($user->getCode() == $form->get('code')->getData()) {
                    $request->getSession()->remove('email');

                    $guardHandler->authenticateUserAndHandleSuccess(
                        $user,
                        $request,
                        $formAuthenticator,
                        'main'
                    );

                    return $this->redirectToRoute('user.config.index');

                }
            }
        }

        else
        {

            $this->addFlash('denied', 'Le code indiqué est incorrect.');
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('forgotten_mdp/confirm_code.html.twig', [
            'form' => $form->createView(),
        ]);

    }

}