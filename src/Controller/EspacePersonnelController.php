<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class EspacePersonnelController extends AbstractController
{
    #[Route('/espacepersonnel', name: 'espace_personnel')]
    public function index(): Response
    {

        dd($this->getUser());



        return $this->render('espace_personnel/index.html.twig', [
        ]);

    }
}
