<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Sector;
use App\Entity\Skills;
use App\Entity\Worker;
use DateTimeInterface;
use App\Entity\Company;
use App\Entity\Province;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        
        $admin = new User();
        $admin->setUsername('duboismax')
            ->setEmail('duboismax01@gmail.com')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'aaaaaa'))
            ->setRoles(['ROLE_ADMIN'])
            ->setImage('istockphoto1300845620612x6126467439d9af5f-646759f3d64e1.jpg');

        $manager->persist($admin);

        $worker = new Worker();
        $worker->setFirstname('Maxime')
            ->setLastname('Dubois')
            ->setAge(new DateTime('28-02-1996'))
            ->setGender('Homme')
            ->setDescription('lorem10')
            ->setVisibility('1')
            ->setUser($admin);

        $manager->persist($worker);

        $activity = [
            'Agriculture',
            'Agroalimentaire',
            'Art et culture',
            'Assurance',
            'Banque',
            'Bâtiment et construction',
            'Beauté et bien-être',
            'Chimie',
            'Commerce de détail',
            'Commerce de gros',
            'Communication et médias',
            'Éducation',
            'Énergie',
            'Environnement',
            'Finance',
            'Hôtellerie et restauration',
            'Industrie automobile',
            'Industrie pharmaceutique',
            "Informatique et technologies de l'information",
            'Ingénierie',
            'Logistique et transport',
            'Marketing et publicité',
            'Santé et services sociaux',
            'Services aux entreprises',
            'Services juridiques',
            'Services publics',
            'Télécommunications',
            'Tourisme et loisirs',
            'Vente et distribution',
        ];

        foreach ($activity as $name) {
            $sector = new Sector();
            $sector->setName($name);

            $manager->persist($sector);
        }

        $manager->flush();

        $skillsBySector = [
            'Agriculture' => [
                'Agriculture biologique',
                'Horticulture',
                'Viticulture',
                'Élevage',
                'Agriculture de précision',
            ],
            'Agroalimentaire' => [
                'Transformation des aliments',
                'Contrôle qualité alimentaire',
                'Sécurité alimentaire',
                'R&D alimentaire',
                'Gestion de production agroalimentaire',
            ],
            'Art et culture' => [
                'Peinture',
                'Sculpture',
                'Photographie',
                'Danse',
                'Musique',
            ],
            'Assurance' => [
                'Assurance vie',
                'Assurance automobile',
                'Assurance habitation',
                'Gestion des sinistres',
                'Analyse des risques',
            ],
            'Banque' => [
                'Gestion de patrimoine',
                'Prêts et crédits',
                'Services bancaires en ligne',
                'Conseil en investissement',
                'Risk management',
            ],
            'Bâtiment et construction' => [
                'Génie civil',
                'Conception architecturale',
                'Gestion de chantier',
                'Maçonnerie',
                'Électricité du bâtiment',
            ],
            'Beauté et bien-être' => [
                'Coiffure',
                'Esthétique',
                'Massage',
                'Maquillage',
                'Nutrition et diététique',
            ],
            'Chimie' => [
                'Synthèse organique',
                'Analyse chimique',
                'Chimie industrielle',
                'Chimie des matériaux',
                'Chimie environnementale',
            ],
            'Commerce de détail' => [
                'Gestion de magasin',
                'Merchandising',
                'Service client',
                'Vente en ligne',
                'Gestion des stocks',
            ],
            'Commerce de gros' => [
                'Négociation commerciale',
                'Gestion des achats',
                'Logistique de distribution',
                'Relations fournisseurs',
                'Analyse de marché B2B',
            ],
            'Communication et médias' => [
                'Relations publiques',
                'Journalisme',
                'Marketing digital',
                'Production audiovisuelle',
                'Stratégies de communication',
            ],
            'Éducation' => [
                'Enseignement primaire',
                'Enseignement secondaire',
                'Formation professionnelle',
                'Pédagogie',
                'Éducation spécialisée',
            ],
            'Énergie' => [
                'Énergies renouvelables',
                'Énergie solaire',
                'Énergie éolienne',
                "Distribution d'électricité",
                'Gestion des ressources énergétiques',
            ],
            'Environnement' => [
                'Gestion des déchets',
                "Étude d'impact environnemental",
                'Protection de la biodiversité',
                'Énergie verte',
                'Écologie industrielle',
            ],
            'Finance' => [
                'Analyse financière',
                'Gestion de portefeuille',
                'Comptabilité',
                'Fiscalité',
                'Audit financier',
            ],
            'Hôtellerie et restauration' => [
                'Gestion hôtelière',
                'Service en salle',
                'Cuisine et gastronomie',
                'Réceptionniste',
                'Sommellerie',
            ],
            'Industrie automobile' => [
                'Conception automobile',
                'Ingénierie automobile',
                'Fabrication automobile',
                'Contrôle qualité automobile',
                'Maintenance automobile',
            ],
            'Industrie pharmaceutique' => [
                'Recherche et développement',
                'Production pharmaceutique',
                'Contrôle qualité pharmaceutique',
                'Affaires réglementaires',
                'Pharmacovigilance',
            ],
            'Informatique et technologies de l\'information' => [
                'Développement web',
                'Développement mobile',
                'Administration système',
                'Cybersécurité',
                'Intelligence artificielle',
            ],
            'Ingénierie' => [
                'Ingénierie mécanique',
                'Ingénierie électrique',
                'Ingénierie civile',
                'Ingénierie des systèmes',
                'Ingénierie industrielle',
            ],
            'Logistique et transport' => [
                'Gestion de la chaîne logistique',
                'Transport maritime',
                'Transport aérien',
                'Transport ferroviaire',
                'Logistique internationale',
            ],
            'Marketing et publicité' => [
                'Stratégie marketing',
                'Marketing digital',
                'Publicité en ligne',
                'Études de marché',
                'Communication publicitaire',
            ],
            'Santé et services sociaux' => [
                'Médecine générale',
                'Infirmière',
                'Kinésithérapie',
                'Assistance sociale',
                'Psychologie',
            ],
            'Services aux entreprises' => [
                'Consulting',
                'Gestion de projet',
                'Ressources humaines',
                'Formation professionnelle',
                'Conseil en stratégie',
            ],
            'Services juridiques' => [
                'Droit des affaires',
                'Droit civil',
                'Droit pénal',
                'Propriété intellectuelle',
                'Contentieux',
            ],
            'Services publics' => [
                'Administration publique',
                'Services municipaux',
                'Services sociaux',
                'Sécurité publique',
                'Justice',
            ],
            'Télécommunications' => [
                'Réseaux télécoms',
                'Téléphonie mobile',
                'Fibre optique',
                'Gestion des services télécoms',
                'Ingénierie télécoms',
            ],
            'Tourisme et loisirs' => [
                'Agence de voyage',
                'Tourisme durable',
                'Hôtellerie de luxe',
                'Animation touristique',
                'Guide touristique',
            ],
            'Vente et distribution' => [
                'Négociation commerciale',
                'Gestion des ventes',
                'Distribution de produits',
                'Marketing des ventes',
                'Prospection commerciale',
            ],
        ];

        foreach ($skillsBySector as $sectorName => $skills) {
            $sector = $manager->getRepository(Sector::class)->findOneBy(['name' => $sectorName]);
            foreach ($skills as $skillName) {
                $skill = new Skills();
                $skill->setName($skillName);
                $skill->setSector($sector);
                $manager->persist($skill);
            }
        }

        $manager->flush();

        $provincesNames = [
            'Anvers',
            'Limbourg',
            'Flandre-Occidentale',
            'Flandre-Orientale',
            'Brabant flamand',
            'Bruxelles-Capitale',
            'Hainaut',
            'Liège',
            'Luxembourg',
            'Namur',
            'Brabant wallon',
        ];

        foreach ($provincesNames as $name) {
            $province = new Province();
            $province->setName($name);
            $manager->persist($province);
        
            $provincesEntities[$name] = $province;
        }
        
        $company = new Company();
        $company->setName('dubdub')
                ->setEMail('dubdub@gmail.com')
                ->setCover('istockphoto-517188688-612x612.jpg')
                ->setDescription('lorem10')
                ->setProvinceName($provincesEntities['Brabant wallon'])
                ->setVisibility('1')
                ->setUser($admin);

        $manager->persist($company);

        $manager->flush();
    }
}
