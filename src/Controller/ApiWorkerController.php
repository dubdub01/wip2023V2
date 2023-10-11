<?php

namespace App\Controller;

use App\Entity\Worker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ApiWorkerController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $worker = new Worker();
        $user = $this->getUser(); // Récupération de l'utilisateur connecté

        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);

         // Vérification si l'utilisateur a déjà un worker
    if ($user->getWorkers()->count() > 0) {
        $this->addFlash("danger", "Malheureusement, vous ne pouvez avoir qu'un seul worker.");
        return $this->redirectToRoute('workers_index');
    }

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $cv = $form->get('cv')->getData();
            if ($cv) {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger -> slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();

                try{
                    $cv->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        "something went wrong"
                    );
                }

                $worker->setCv($newFilename);

            }


            $user = $this->getUser();
            $worker->setUser($user);
            $manager->persist($worker);
            $manager->flush();

            $this->addFlash(
                'success',
                "votre worker à bien été créé {$worker->getFirstname()}"
            );
            return $worker;
        }

        

    }
}
