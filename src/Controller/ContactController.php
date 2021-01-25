<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact.index")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */

    public function contactMail(Request $request, MailerInterface $mailer): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $mail = (new Email())
                ->from(new Address($email = $form->get('email')->getData(), $nom = $form->get('nom')->getData()))
                ->to('destinataire@demo.test')
                ->subject($objet = $form->get('objet')->getData())
                ->text($message = $form->get('message')->getData())

            ;

            $mailer->send($mail);

            $this->addFlash('success', 'Votre demande a bien été enregistré.');
            return $this->redirect($request->headers->get('referer'));

        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
