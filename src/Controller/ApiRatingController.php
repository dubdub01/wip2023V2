<?php

namespace App\Controller;

use App\Entity\Worker;
use App\Entity\Rating;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiRatingController extends AbstractController
{
    // #[Route('/api/rating', name: 'app_api_rating')]
    public function __invoke(EntityManagerInterface $entityManager, int $id, Request $request): JsonResponse
    {
        // Récupérez le travailleur
        $worker = $entityManager->getRepository(Worker::class)->find($id);
    
        if (!$worker) {
            return new JsonResponse(['message' => 'Worker introuvable.'], Response::HTTP_NOT_FOUND);
        }
    
        // Récupérez l'utilisateur qui a noté le travailleur
        $user = $this->getUser();
    
        // Assurez-vous que l'utilisateur existe et est connecté
        if ($user) {
            // Supprimez le travailleur de la liste 'hasContacted' de l'utilisateur
            $user->removeHasContacted($worker);
    
            // Enregistrez les modifications dans la base de données
            $entityManager->flush();
    
            // Ajoutez la note au travailleur
            
            $entityManager->flush();
    
            return new JsonResponse(['message' => 'Note ajoutée et worker supprimé de la liste des contacts.'], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
