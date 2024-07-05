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

    public function getExerciseById(int $exerciseId): object
    {
        return $this->exerciseRepository->find($exerciseId);
    }

    public function getExercisesByMuscleGroup($muscleGroup): array
    {
        return $this->exerciseRepository->findByMuscleGroup($muscleGroup);
    }

    public function addExercise(Exercise $exercise): array
    {
        try {
            $existingExercise = $this->exerciseRepository->findByName($exercise->getName());

            if ($existingExercise) {
                throw new \Exception('An exercise with this name already exists!');
            }
            $this->exerciseRepository->create($exercise);

            return ['success' => true, 'message' => 'Exercise created successfully!'];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    public function updateExercise(Exercise $exercise): array
    {
        try {
            $existingExercise = $this->exerciseRepository->findByNameExcludingId($exercise->getName(), $exercise->getId());

            if ($existingExercise) {
                throw new \Exception('An exercise with this name already exists!');
            }
            $this->exerciseRepository->update($exercise);

            return ['success' => true, 'message' => 'Exercise updated successfully!'];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    public function deleteExercise(int $id)
    {
        $this->exerciseRepository->delete($id);
    }

    public function getExercisesByWorkout($id)
    {
        return $this->exerciseRepository->findByWorkout($id);
    }
}