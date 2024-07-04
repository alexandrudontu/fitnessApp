<?php

namespace App\Service;

use App\Entity\MuscleGroup;
use App\Repository\MuscleGroupRepository;

class MuscleGroupService
{
    private MuscleGroupRepository $muscleGroupRepository;

    public function __construct(MuscleGroupRepository $muscleGroupRepository)
    {
        $this->muscleGroupRepository = $muscleGroupRepository;
    }

    public function addMuscleGroup(MuscleGroup $muscleGroup): array
    {
        try {
            $existingMuscleGroup = $this->muscleGroupRepository->findByName($muscleGroup->getName());

            if ($existingMuscleGroup) {
                throw new \Exception('A muscle group with this name already exists!');
            }
            $this->muscleGroupRepository->create($muscleGroup);

            return ['success' => true, 'message' => 'Muscle group created successfully!'];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}