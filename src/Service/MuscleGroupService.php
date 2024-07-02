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
        $existingMuscleGroup = $this->muscleGroupRepository->findByName($muscleGroup->getName());
        if ($existingMuscleGroup) {
            return ['success' => false, 'message' => 'A muscle group with this name already exists!'];
        }

        $this->muscleGroupRepository->create($muscleGroup);
        return ['success' => true, 'message' => 'Muscle group created successfully!'];
    }
}