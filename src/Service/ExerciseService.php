<?php

namespace App\Service;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;

class ExerciseService
{
    private ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    public function addExercise(Exercise $exercise): array
    {
        $existingExercise = $this->exerciseRepository->findByName($exercise->getName());
        if ($existingExercise) {
            return ['success' => false, 'message' => 'An exercise with this name already exists!'];
        }

        $this->exerciseRepository->create($exercise);
        return ['success' => true, 'message' => 'Exercise created successfully!'];
    }
}