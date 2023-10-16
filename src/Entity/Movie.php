<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

// todo: add validation for "publication_on" (Y-m-d) and make sure it's stored in ISO 8601 format

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Assert\Length(
        min: 1,
        max: 128,
        minMessage: 'Your name must be at least {{ limit }} characters long',
        maxMessage: 'Your name cannot be longer than {{ limit }} characters',
        groups: ['movie:create', 'movie:update']
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, length: 2048, nullable: true)]
    #[Assert\Length(
        min: 1,
        max: 2048,
        minMessage: 'Your description must be at least {{ limit }} characters long',
        maxMessage: 'Your description cannot be longer than {{ limit }} characters',
        groups: ['movie:create', 'movie:update']
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $publication_on = null;

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
}
