<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RatingRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\ApiAccountController;
use App\Controller\ApiRatingController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[ApiResource(
    normalizationContext:['groups'=>['user:read']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Post(
            controller: ApiRatingController::class,
            uriTemplate: '/ratings/{id}/rating',
            name: 'ratingPost',
            openapiContext:[
                "summary"=> "Ajouer une note au worker et le supprimer de la liste des contact",
                "description" => "Ajouter une note au worker et le supprimer de la liste des contact"
            ],
            deserialize:false
        )
    ]
)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups (['user:read'])]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'ratings')]

    private ?Worker $worker = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getWorker(): ?Worker
    {
        return $this->worker;
    }

    public function setWorker(?Worker $worker): static
    {
        $this->worker = $worker;

        return $this;
    }
}
