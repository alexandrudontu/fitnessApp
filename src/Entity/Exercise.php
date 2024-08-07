<?php

namespace App\Entity;

use App\Repository\ExerciseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    #[ORM\ManyToOne(inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MuscleGroup $muscleGroup = null;

    #[ORM\Column]
    private ?bool $isBodyWeight = null;

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

    public function getMuscleGroup(): ?MuscleGroup
    {
        return $this->muscleGroup;
    }

    public function setMuscleGroup(?MuscleGroup $muscleGroup): static
    {
        $this->muscleGroup = $muscleGroup;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIsBodyWeight(): bool
    {
        return $this->isBodyWeight;
    }

    public function setIsBodyWeight(bool $isBodyWeight): static
    {
        $this->isBodyWeight = $isBodyWeight;

        return $this;
    }
}
