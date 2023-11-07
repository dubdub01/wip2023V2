<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ApiUploadImageController extends AbstractController
{
    #[Route('/api/upload/image', name: 'app_api_upload_image')]
    public function __invoke(Request $request, SluggerInterface $slugger, $id, ManagerRegistry $doctrine)
    {
        // Récupérez le travailleur associé à l'ID
        $company = $doctrine->getRepository(Company::class)->find($id);

        // Vérifiez si le travailleur existe
        if (!$company) {
            return $this->json(['message' => 'Entreprise non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Récupérez le fichier CV depuis la requête
        $coverFile = $request->files->get('cover');

        // Vérifiez si un fichier a été téléchargé
        if (!$coverFile) {
            return $this->json(['message' => 'Aucune image trouvé'], Response::HTTP_BAD_REQUEST);
        }

        // Générez un nom de fichier sécurisé
        $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $coverFile->guessExtension();

        // Essayez de déplacer le fichier vers le répertoire souhaité
        try {
            $coverFile->move(
                $this->getParameter('images_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            return $this->json(['message' => 'Erreur lors de la sauvegarde du fichier image'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Mettez à jour le chemin du CV du travailleur dans la base de données
        $company->setCover($newFilename);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json(['message' => 'image ajouté avec succès'], Response::HTTP_OK);
    }
}
