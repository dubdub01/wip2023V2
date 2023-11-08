<?php

namespace App\Controller;

use App\Entity\Worker;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiWorkerMailController extends AbstractController
{
    // #[Route('/worker/{slug}/email', name: 'worker_email')]
    public function __invoke(MailerInterface $mailer, Worker $worker, EntityManagerInterface $manager, Request $request): JsonResponse
    {

        $creatorEmail = $worker->getUser()->getEmail();
        $user = $this->getUser();
        $email = (new Email())
            ->from('noreply@wip.be')
            ->to($creatorEmail)
            ->subject('Votre profil intéresse')
            ->text('Bonjour ' . $worker->getUser()->getUsername() . ' un utilisateur est intéressé par votre profil de worker, vous pouvez le contacter sur cette adresse mail : '.$user->getEmail())
            ->html('<p>See Twig integration for better HTML integration!</p>');
        $data = json_decode($request->getContent(), true);

        try {

            $mailer->send($email);

            $user = $this->getUser();
            if ($user) {
                $user->addHasContacted($worker);
                $manager->persist($user);
                $manager->flush();
            }

            $this->addFlash(
                'success',
                'Mail Envoyé'
            );

            return new JsonResponse(['message' => 'E-mail envoyé avec succès'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de l\'envoi de l\'e-mail'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
