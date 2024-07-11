<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Form\ExerciseType;
use App\Repository\ExerciseLogRepository;
use App\Repository\ExerciseRepository;
use App\Repository\MuscleGroupRepository;
use App\Repository\WorkoutRepository;
use App\Service\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExerciseController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/exercise/create', name: 'app_exercise')]
    public function create(Request $request, ExerciseService $exerciseService): Response
    {
        $exercise = new Exercise();

        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $exercise = $form->getData();
            $imageFile = $form->get('image_file')->getData();
            $result = $exerciseService->addExercise($exercise, $imageFile);

            if (!$result['success']) {
                $this->addFlash('error', $result['message']);
                return $this->redirectToRoute('app_exercise');
            }

            // ... perform some action, such as saving the task to the database
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('show_exercises');
        }

        return $this->render('exercise/create.html.twig', [
            'form' => $form,
            'action' => 'create',
        ]);
    }

    #[Route('/exercise', name: 'show_exercises')]
    public function show(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAll();

        return $this->render('exercise/show.html.twig', [
            'exercises' => $exercises,
            'type' =>'exercise',
            ]);
    }

    #[Route('/muscle/group/{id}/exercises', name: 'muscle_group_exercises')]
    public function showMuscleGroupExercises(ExerciseService $exerciseService, MuscleGroupRepository $muscleGroupRepository, $id): Response
    {
        $muscleGroup = $muscleGroupRepository->find($id);

        $user = $this->security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You are not logged in.');
        }

        if (!$muscleGroup) {
            throw $this->createNotFoundException('Muscle group not found');
        }

        $exercises = $exerciseService->getExercisesByMuscleGroup($id);

        return $this->render('exercise/show.html.twig', [
            'muscleGroup' => $muscleGroup,
            'exercises' => $exercises,
            'type' => 'muscle',
        ]);
    }

    #[Route('/workout/{id}/exercises', name: 'workout_exercises')]
    public function showWorkoutExercises(ExerciseService $exerciseService, WorkoutRepository $workoutRepository, $id): Response
    {
        $workout = $workoutRepository->find($id);

        if (!$workout) {
            throw $this->createNotFoundException('Workout not found');
        }

        $exercises = $exerciseService->getExercisesByWorkout($id);

        return $this->render('exercise/showLog.html.twig', [
            'workout' => $workout,
            'exercises' => $exercises,
        ]);
    }

    #[Route('/exercise/{id}', name: 'edit_exercise', methods: ['GET', 'POST'])]
    public function update(Request $request, ExerciseService $exerciseService, int $id): Response
    {
        $exercise = $exerciseService->getExerciseById($id);
        if (!$exercise) {
            throw $this->createNotFoundException('No exercise found for id ' . $id);
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You are not logged in.');
        }

        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = $form->getData();
            $imageFile = $form->get('image_file')->getData();
            $result = $exerciseService->updateExercise($exercise, $imageFile);
            if (!$result['success']) {
                $this->addFlash('error', $result['message']);
                return $this->redirectToRoute('edit_exercise', ['id' => $id]);
            }

            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('show_exercises');
        }

        return $this->render('exercise/create.html.twig', [
            'form' => $form,
            'action' => 'update',
        ]);
    }

    #[Route('/exercise/delete/{id}', name: 'delete_exercise', methods: ['DELETE'])]
    public function destroy(Request $request, ExerciseService $exerciseService, int $id)
    {
        $exerciseService->deleteExercise($exerciseService->getExerciseById($id)->getId());
        return $this->redirectToRoute('show_exercises');
    }


    #[Route('/exercise/{id}/logs', name: 'exercise_logs')]
    public function showExerciseLogs(ExerciseLogRepository $exerciseLogRepository, ExerciseService $exerciseService, int $id): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You are not logged in.');
        }

        $logs = $exerciseLogRepository->findLogsByExerciseAndUser($id, $user->getId());
        $exerciseName = $exerciseService->getExerciseById($id)->getName();

        return $this->render('exercise/logs.html.twig', [
            'logs' => $logs,
            'exerciseName' => $exerciseName,
        ]);
    }

}

