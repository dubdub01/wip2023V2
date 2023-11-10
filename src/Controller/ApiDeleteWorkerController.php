<?php

namespace App\Controller;

use App\Entity\Worker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiDeleteWorkerController extends AbstractController
{
    public function __invoke(Worker $worker, EntityManagerInterface $entityManager): JsonResponse
{
    // Supprimer toutes les évaluations associées
    foreach ($worker->getRatings() as $rating) {
        $entityManager->remove($rating);
    }

    // Supprimer le travailleur
    $entityManager->remove($worker);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Le travailleur a été supprimé avec succès'], JsonResponse::HTTP_OK);
}
}
