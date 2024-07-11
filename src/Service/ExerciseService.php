<?php

namespace App\Service;

use App\Entity\Exercise;
use App\Entity\Image;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ExerciseService
{
    private ExerciseRepository $exerciseRepository;
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;
    private string $imagesDirectory;

    public function __construct(EntityManagerInterface $entityManager, ExerciseRepository $exerciseRepository, SluggerInterface $slugger, string $imagesDirectory)
    {
        $this->entityManager = $entityManager;
        $this->exerciseRepository = $exerciseRepository;
        $this->slugger = $slugger;
        $this->imagesDirectory = $imagesDirectory;
    }
    public function getExerciseById(int $exerciseId): object
    {
        return $this->exerciseRepository->find($exerciseId);
    }

    public function getExercisesByMuscleGroup($muscleGroup): array
    {
        return $this->exerciseRepository->findByMuscleGroup($muscleGroup);
    }

    public function addExercise(Exercise $exercise, ?UploadedFile $imageFile): array
    {
        try {
            $existingExercise = $this->exerciseRepository->findByName($exercise->getName());

            if ($existingExercise) {
                throw new \Exception('An exercise with this name already exists!');
            }
            if ($imageFile) {
                $this->handleImageUpload($exercise, $imageFile);
            }
            $this->exerciseRepository->create($exercise);

            return ['success' => true, 'message' => 'Exercise created successfully!'];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    public function updateExercise(Exercise $exercise, ?UploadedFile $imageFile): array
    {
        try {
            $existingExercise = $this->exerciseRepository->findByNameExcludingId($exercise->getName(), $exercise->getId());
            if ($existingExercise) {
                throw new \Exception('An exercise with this name already exists!');
            }
            if ($imageFile) {
                $this->handleImageUpload($exercise, $imageFile);
            }
            $this->exerciseRepository->update($exercise);

            return ['success' => true, 'message' => 'Exercise updated successfully!'];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    private function handleImageUpload(Exercise $exercise, UploadedFile $imageFile): void
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->imagesDirectory,
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('Could not move the file: '.$e->getMessage());
        }

        $image = new Image();
        $image->setPath($newFilename);
        $exercise->setImage($image);
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