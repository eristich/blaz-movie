<?php

namespace App\Entity;

use App\Repository\RelActorRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RelActorRepository::class)]
#[ORM\UniqueConstraint('actor_composite_idx', columns: ['person_id', 'movie_id'])]
class RelActor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'actors')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['movie:get-one'])]
    private ?Person $person = null;

    #[ORM\ManyToOne(inversedBy: 'actors')]
    private ?Movie $movie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;

        return $this;
    }
}
