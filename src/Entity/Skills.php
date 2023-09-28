<?php

namespace App\Entity;

use App\Entity\Sector;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SkillsRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SkillsRepository::class)]
#[ApiResource(
    normalizationContext:['groups'=>['user:read']],
)]
class Skills
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups (['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
        #[Groups (['user:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Sector::class, inversedBy: 'skills')]
    private ?Sector $sector = null;

    #[ORM\ManyToMany(targetEntity: Worker::class, mappedBy: 'skills')]
    private Collection $workers;

    public function __construct()
    {
        $this->workers = new ArrayCollection();
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

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return Collection<int, Worker>
     */
    public function getWorkers(): Collection
    {
        return $this->workers;
    }

    public function addWorker(Worker $worker): self
    {
        if (!$this->workers->contains($worker)) {
            $this->workers->add($worker);
            $worker->addSkill($this);
        }

        return $this;
    }

    public function removeWorker(Worker $worker): self
    {
        if ($this->workers->removeElement($worker)) {
            $worker->removeSkill($this);
        }

        return $this;
    }
}
