<?php

namespace App\Entity;

use App\Entity\Province;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext:['groups'=>['user:read']],
)]

class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $eMail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $cover = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $slug = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $visibility = null;

    #[ORM\ManyToOne(inversedBy: 'company')]
    #[Groups(['company:read'])]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Sector::class, inversedBy: 'companies')]
    #[Groups(['user:read'])]
    private Collection $sector;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    #[Groups(['user:read'])]
    private ?Province $provinceName = null;

    public function __construct()
    {
        $this->sector = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug():void
    {
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name.''.uniqid());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEMail(): ?string
    {
        return $this->eMail;
    }

    public function setEMail(string $eMail): self
    {
        $this->eMail = $eMail;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
     * @return Collection<int, Sector>
     */
    public function getSector(): Collection
    {
        return $this->sector;
    }

    public function addSector(Sector $sector): self
    {
        if (!$this->sector->contains($sector)) {
            $this->sector->add($sector);
        }

        return $this;
    }

    public function removeSector(Sector $sector): self
    {
        $this->sector->removeElement($sector);

        return $this;
    }

    public function getProvinceName(): ?Province
    {
        return $this->provinceName;
    }

    public function setProvinceName(?Province $provinceName): self
    {
        $this->provinceName = $provinceName;

        return $this;
    }

    
}
