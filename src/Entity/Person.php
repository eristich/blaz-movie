<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['movie:get-one'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['movie:get-one'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['movie:get-one'])]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['movie:get-one'])]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: RelDirector::class)]
    private Collection $directors;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: RelActor::class)]
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

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
            $director->setPerson($this);
        }

        return $this;
    }

    public function removeDirector(RelDirector $director): static
    {
        if ($this->directors->removeElement($director)) {
            // set the owning side to null (unless already changed)
            if ($director->getPerson() === $this) {
                $director->setPerson(null);
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
            $actor->setPerson($this);
        }

        return $this;
    }

    public function removeActor(RelActor $actor): static
    {
        if ($this->actors->removeElement($actor)) {
            // set the owning side to null (unless already changed)
            if ($actor->getPerson() === $this) {
                $actor->setPerson(null);
            }
        }

        return $this;
    }
}
