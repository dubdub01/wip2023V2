<?php

namespace App\Controller;

use App\Entity\Worker;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ApiUploadCVController extends AbstractController
{
    /**
     * @Route("/api/workers/{id}/upload", name="workerUploadPost", methods={"POST"})
     */
    public function __invoke(Request $request, SluggerInterface $slugger, $id, ManagerRegistry $doctrine)
    {
        // Récupérez le travailleur associé à l'ID
        $worker = $doctrine->getRepository(Worker::class)->find($id);

        // Vérifiez si le travailleur existe
        if (!$worker) {
            return $this->json(['message' => 'Travailleur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Récupérez le fichier CV depuis la requête
        $cvFile = $request->files->get('cv');

        // Vérifiez si un fichier a été téléchargé
        if (!$cvFile) {
            return $this->json(['message' => 'Aucun fichier CV trouvé'], Response::HTTP_BAD_REQUEST);
        }

        // Générez un nom de fichier sécurisé
        $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

        // Essayez de déplacer le fichier vers le répertoire souhaité
        try {
            $cvFile->move(
                $this->getParameter('cv_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            return $this->json(['message' => 'Erreur lors de la sauvegarde du fichier CV'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Mettez à jour le chemin du CV du travailleur dans la base de données
        $worker->setCv($newFilename);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($worker);
        $entityManager->flush();

        return $this->json(['message' => 'CV ajouté avec succès'], Response::HTTP_OK);
    }
}
