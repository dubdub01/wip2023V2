<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Worker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HasContactedController extends AbstractController
{
    /**
     * @Route("/api/users/{userId}/workers/{workerId}/has-contacted", name="app_remove_has_contacted", methods={"DELETE"})
     */
    public function removeToContacted(User $user, Worker $worker, EntityManagerInterface $entityManager)
    {
        // Votre logique de suppression de l'entité hasContacted ici
        $user->removeHasContacted($worker);
        $entityManager->flush();

        return new JsonResponse(['message' => 'L\'entité hasContacted a été supprimée avec succès.']);
    }



    /**
     * @Route("/api/users/{userId}/add-worker-to-contacted/{workerId}", name="add_worker_to_contacted", methods={"POST"})
     */
    public function addWorkerToContacted(User $user, Worker $worker, EntityManagerInterface $entityManager)
    {
        // Votre logique d'ajout du worker à la liste des travailleurs contactés de l'utilisateur ici
        $user->addHasContacted($worker);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Le travailleur a été ajouté à la liste des travailleurs contactés.']);
    }
}
