<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Workout;
use App\Repository\WorkoutRepository;

class WorkoutService
{
    private WorkoutRepository $workoutRepository;

    public function __construct(WorkoutRepository $workoutRepository)
    {
        $this->workoutRepository = $workoutRepository;
    }
    public function store(Workout $workout): array
    {
        try {
            $existingWorkout = $this->workoutRepository->findByName($workout->getName());

            if ($existingWorkout) {
                throw new \Exception('A workout with this name already exists!');
            }
            $this->workoutRepository->create($workout);

            return ['success' => true, 'message' => 'Workout created successfully!'];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    public function findByUser(?User $user)
    {
        return $this->workoutRepository->findBy(['person' => $user]);
    }

    public function getWorkoutById(int $workoutId)
    {
        return $this->workoutRepository->find($workoutId);
    }

    public function deleteWorkout(int $id): void
    {
        $this->workoutRepository->delete($id);
    }
}