<?php

namespace App\Entity;

use App\Entity\Skills;
use Cocur\Slugify\Slugify;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\MailerController;
use App\Repository\WorkerRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\ApiWorkerController;
use App\Controller\ApiWorkerMailController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\ApiUploadCVController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext:['groups'=>['user:read', 'worker:read']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
        new Patch(),
        new Post(
            controller: ApiWorkerMailController::class,
            uriTemplate: '/workers/{id}/email',
            name: 'workerMailPost',
            openapiContext:[
                "summary"=> "envoyer un mail",
                "description" => "envoyer un mail"
            ],
            deserialize:false
        ),
        new Post(
            controller: ApiUploadCVController::class,
            uriTemplate: '/workers/{id}/upload',
            name: 'workerUploadPost',
            openapiContext:[
                "summary"=> "Ajouer un cv au worker",
                "description" => "Ajouter un cv au worker"
            ],
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties:[
    "sector",
    "visibility"
])]
#[ApiFilter(OrderFilter::class)]


class Worker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['user:read'])]

    private ?\DateTimeInterface $age = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]

    private ?string $gender = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['user:read'])]

    private ?bool $visibility = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $cv = null;

    #[ORM\ManyToMany(targetEntity: Skills::class, inversedBy: 'workers')]
    #[Groups(['worker:read'])]
    private Collection $skills;

    #[ORM\ManyToOne(inversedBy: 'workers')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'worker', targetEntity: Rating::class)]
    #[Groups(['worker:read'])]
    private Collection $ratings;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'hasContacted')]
    private Collection $contacted;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->contacted = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstname.' '.$this->lastname.' '.uniqid());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAge(): ?\DateTimeInterface
    {
        return $this->age;
    }

    public function setAge(\DateTimeInterface $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * @return Collection<int, Skills>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skills $skill): self
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setWorker($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getWorker() === $this) {
                $rating->setWorker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getContacted(): Collection
    {
        return $this->contacted;
    }

    public function addContacted(User $contacted): static
    {
        if (!$this->contacted->contains($contacted)) {
            $this->contacted->add($contacted);
            $contacted->addHasContacted($this);
        }

        return $this;
    }

    public function removeContacted(User $contacted): static
    {
        if ($this->contacted->removeElement($contacted)) {
            $contacted->removeHasContacted($this);
        }

        return $this;
    }

}
