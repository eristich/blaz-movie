<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

// todo: add validation for "publication_on" (Y-m-d) and make sure it's stored in ISO 8601 format

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['movie:get-one', 'movie:get-many'])]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(['movie:get-one', 'movie:get-many', 'movie:create', 'movie:update'])]
    #[Assert\Length(
        min: 1,
        max: 128,
        minMessage: 'Your name must be at least {{ limit }} characters long',
        maxMessage: 'Your name cannot be longer than {{ limit }} characters',
        groups: ['movie:create', 'movie:update']
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, length: 2048, nullable: true)]
    #[Groups(['movie:get-one', 'movie:get-many', 'movie:create', 'movie:update'])]
    #[Assert\Length(
        min: 1,
        max: 2048,
        minMessage: 'Your description must be at least {{ limit }} characters long',
        maxMessage: 'Your description cannot be longer than {{ limit }} characters',
        groups: ['movie:create', 'movie:update']
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['movie:get-one', 'movie:get-many', 'movie:create', 'movie:update'])]
    private ?\DateTimeInterface $publication_on = null;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: RelDirector::class)]
    private Collection $directors;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: RelActor::class)]
    private Collection $actors;

    public function __construct()
    {
        $this->directors = new ArrayCollection();
        $this->actors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPublicationOn(): ?\DateTimeInterface
    {
        return $this->publication_on;
    }

    public function setPublicationOn(\DateTimeInterface $publication_on): static
    {
        $this->publication_on = $publication_on;

        return $this;
    }

    /**
     * @return Collection<int, RelDirector>
     */
    public function getDirectors(): Collection
    {
        return $this->directors;
    }

    public function addDirector(RelDirector $director): static
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->setMovie($this);
        }

        return $this;
    }

    public function removeDirector(RelDirector $director): static
    {
        if ($this->directors->removeElement($director)) {
            // set the owning side to null (unless already changed)
            if ($director->getMovie() === $this) {
                $director->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RelActor>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(RelActor $actor): static
    {
        if (!$this->actors->contains($actor)) {
            $this->actors->add($actor);
            $actor->setMovie($this);
        }

        return $this;
    }

    public function removeActor(RelActor $actor): static
    {
        if ($this->actors->removeElement($actor)) {
            // set the owning side to null (unless already changed)
            if ($actor->getMovie() === $this) {
                $actor->setMovie(null);
            }
        }

        return $this;
    }
}
