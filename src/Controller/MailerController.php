<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\Worker;
use App\Form\MailerType;
use Symfony\Component\Mime\Email;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerController extends AbstractController
{
    #[Route('/worker/{slug}/email', name: 'worker_email')]
    public function sendEmailWorker(MailerInterface $mailer, Worker $worker, EntityManagerInterface $manager, Request $request): Response
    {
        $creatorEmail = $worker->getUser()->getEmail();
        $user = $this->getUser();
        $email = (new Email())
            ->from('noreply@wip.be')
            ->to($creatorEmail)
            ->subject('Votre profil intéresse')
            ->text('Bonjour ' .$worker->getUser()->getUsername(). ' un utilisateur est intéressé par votre profil de worker, vous pouvez le contacter sur cette adresse mail : ' .$user->getEmail())
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        $this->addFlash(
            'success',
            'Mail Envoyé'
        );

        return $this->render('worker/workerPartials.html.twig', [
            "worker" => $worker
        ]);    
    }
    
    #[Route('/company/{slug}/email', name: 'company_email')]
    public function sendEmail(MailerInterface $mailer, Company $company, EntityManagerInterface $manager, Request $request): Response
    {
        $creatorEmail = $company->getUser()->getEmail();
        $user = $this->getUser();
        $email = (new Email())
            ->from('noreply@wip.be')
            ->to($creatorEmail)
            ->subject('Votre profil intéresse')
            ->text('Bonjour ' .$company->getUser()->getUsername(). ' un utilisateur est intéressé par votre company ' .$company->getName(). ', vous pouvez le contacter sur cette adresse mail : ' .$user->getEmail())
            ->html('<p>Bonjour ' .$company->getUser()->getUsername(). ' un utilisateur est intéressé par votre company ' .$company->getName(). ', vous pouvez le contacter sur cette adresse mail : ' .$user->getEmail().' </p>');

        $mailer->send($email);

        $this->addFlash(
            'success',
            'Mail Envoyé'
        );

        return $this->render('company/companyPartials.html.twig', [
            "company" => $company
        ]);
    }
    
}