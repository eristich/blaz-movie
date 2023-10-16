<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DirectorController extends AbstractController
{
    #[Route('/director', name: 'app_director')]
    public function index(): Response
    {
        return $this->render('director/index.html.twig', [
            'controller_name' => 'DirectorController',
        ]);
    }
}
