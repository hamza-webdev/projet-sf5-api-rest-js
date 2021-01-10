<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListController extends AbstractFOSRestController
{
    /**
     * @Route("/list", name="list")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ListController.php',
        ]);
    }

    /**
     * @Rest\Get("/update")
     */
     public function update(): Response
     {
        return $this->json([
            'message' => 'Hellooooooooooooo'
            
        ]);
     }

}
