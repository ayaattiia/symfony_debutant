<?php
// src/Controller/InscriptionController.php
namespace App\Controller;


use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InscriptionController extends AbstractController
{
    #[Route('/Accueil', name: 'app_number')]
    public function number(): Response
    {
        $number = random_int(0, 100);
        return $this->render('inscription/accueil.html.twig', [
            'number' => $number,
        ]);
    }


    #[Route('/voir/{id}', name: 'voir', requirements: ['id' => '\d+'])]
    public function voir(int $id): Response
    {
        return $this->render('inscription/voir.html.twig', [
            'id' => $id,
        ]);
    }
}
