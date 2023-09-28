<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\Company;
use App\Form\CompanyType;
use App\Form\ImgModifyType;
use App\Form\CompanySearchType;
use App\Form\CompanyUpdateType;
use App\Entity\CompanyImgModify;
use App\Repository\SectorRepository;
use App\Repository\CompanyRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Artprima\QueryFilterBundle\QueryFilter\QueryFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Artprima\QueryFilterBundle\QueryFilter\Config\BaseConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CompanyController extends AbstractController
{

    /**
     * Permet d'afficher toutes les companies
     *
     * @param CompanyRepository $repo
     * @return Response
     */
    #[Route("/companies/{page<\d+>?1}", name: 'companies_index')]
    public function index($page, PaginationService $pagination, CompanyRepository $repo, SectorRepository $sectorRepo, Request $request): Response
{
    $pagination->setEntityClass(Company::class)
        ->setPage($page)
        ->setLimit(9);

    $selectedSectorId = $request->query->get('sector');

    $sectors = $sectorRepo->findAll();
    $companies = $repo->findBySector($selectedSectorId);


    return $this->render('company/index.html.twig', [
        'companies' => $companies,
        'sectors' => $sectors,
        'selectedSectorId' => $selectedSectorId,
        'pagination' => $pagination,
    ]);
}

    /**
     * Permet d'afficher une Company
     */
    #[Route('/companies/{slug}', name: 'companies_show')]
    public function show(Company $company): Response
    {
        return $this->render('company/companyPartials.html.twig', [
            'company' => $company
        ]);
    }
    
    /**
     * Permet de modifier une Company
     */
    #[Route('/companies/{slug}/edit', name: 'company_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Company $company): Response
    {
        $form = $this->createForm(CompanyUpdateType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $manager->persist($company);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre Company a bien été modifié {$company->getName()}"
            );

            return $this->redirectToRoute('companies_show', ['slug' => $company->getSlug()]);
        }

        return $this->render("company/edit.html.twig", [
            "company" => $company,
            "myform" => $form->createView()
        ]);
    }

    /**
     * Permet d'ajouter une Company
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/company/new", name: "company_create")]
    public function create(Request $request, EntityManagerInterface $manager, SectorRepository $repo): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // gestion de mon image
            $file = $form['cover']->getData();
            if (!empty($file)) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $e->getMessage();
                }
                $company->setCover($newFilename);
            }

            $selectedSectors = $form->get('sector')->getData();

            foreach ($selectedSectors as $sector) {
                $manager->persist($sector);
                $company->addSector($sector);
            }


            $user = $this->getUser();
            $company->setUser($user);
            $manager->persist($company);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre company a bien été créée"
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render("company/new.html.twig", [
            'myform' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une Company
     */
    #[Route("/companies/{slug}/delete", name: "company_delete")]
    public function delete(Company $company, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            "success",
            "Voter Company {$company->getName()} à bien été supprimé"
        );

        $manager->remove($company);
        $manager->flush();

        return $this->redirectToRoute('companies_index');
    }

    /**
     * Modifier l'image de la company
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("companies/{slug}/imgModify",name:"company_modifimg")]
    #[IsGranted("ROLE_USER")]
    public function imgModify(Request $request, EntityManagerInterface $manager, Company $company): Response
    {
        $imgModify = new CompanyImgModify();
        $form = $this->createForm(ImgModifyType::class, $imgModify);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Supprimer l'image précédente du dossier
            if (!empty($company->getCover())) {
                unlink($this->getParameter('images_directory').'/'.$company->getCover());
            }

            $file = $form['newImage']->getData();
            if (!empty($file)) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $e->getMessage();
                }
                $company->setCover($newFilename);
            }

            $manager->persist($company);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre logo a bien été modifié'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render("company/imgModify.html.twig", [
            'company' => $company,
            'myform' => $form->createView()
        ]);
    }
}
