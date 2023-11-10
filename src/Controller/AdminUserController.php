<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AdminUserType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Afficher la liste de tout les user
     *
     * @param UserRepository $repo
     * @return Response
     */
    #[Route('/', name: 'admin_user_index')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(UserRepository $repo): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'users' => $repo->findAll(),

        ]);
    }

    /**
     * Permet à un admin de supprimer un user
     */
    #[Route('/admin/{slug}/delete', name: 'admin_user_delete')]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteUser(User $user, UserRepository $repo, EntityManagerInterface $manager): Response
    {
        if ($user->getUsername() === 'duboismax'){
            $this->addFlash('danger', '<h2>Bien éssayé</h2> <iframe src="https://giphy.com/embed/37nUOlOoYC5FPwthz6" width="480" height="270" frameBorder="0" class="giphy-embed" allowFullScreen></iframe><p><a href="https://giphy.com/gifs/blizzard-diablo-3-death-stare-37nUOlOoYC5FPwthz6">via GIPHY</a></p>');

            return $this->redirectToRoute('admin_user_index');
        }


        $this->addFlash(
            "success",
            "Vous avez bien supprimé le user {$user->getUsername()}"
        );

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_user_index', [
            'users' => $repo->findAll(),

        ]);
    }
    
    /**
     * Permet à un admin de modifier le role d'un user
     */
    #[Route('/admin/{slug}/roles', name: 'admin_user_role')]
    #[IsGranted("ROLE_ADMIN")]
    public function modifRole(Request $request, EntityManagerInterface $manager, User $user, UserRepository $repo): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
         // pour la validation des images ou utiliser une validation Groups
         $fileName = $user->getImage();
         if(!empty($fileName))
         {
             $user->setImage(
                 new File($this->getParameter('images_directory').'/'.$user->getImage())
             );
         } 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setImage($fileName);
            $manager->persist($user);
            $manager->flush();

            if ($user->getUsername() === 'duboismax'){
                $this->addFlash('danger', 'pauvre mortel');
    
                return $this->redirectToRoute('admin_user_index');
            }

            $this->addFlash(
                'success',
                "vous avez bien modifié le Role de {$user->getUsername()}"
            );
            return $this->redirectToRoute('admin_user_index', [
                'users' => $repo->findAll()
            ]);
        }

        return $this->render("admin_user/edit.html.twig", [
            "myform" => $form->createView()
        ]);
    }
}