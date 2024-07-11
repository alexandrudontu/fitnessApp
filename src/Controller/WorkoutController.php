<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Workout;
use App\Form\WorkoutType;
use App\Service\WorkoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkoutController extends AbstractController
{
    #[Route('workout/create', name: 'app_workout')]
    public function store(Request $request, WorkoutService $workoutService): Response
    {
        $user = $this->getUser();
        if(!$user) {
            $this->addFlash('error', 'You have to be logged in to create a workout!');
            return $this->redirectToRoute('show_workouts');
        }
        $workout = new Workout();

        $form = $this->createForm(WorkoutType::class, $workout);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Workout $workout */
            $workout = $form->getData();

            /** @var User $user */
            $user = $this->getUser();
            if($user) {
                $workout->setPerson($user);
            }
            $workoutService->store($workout);

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('show_workouts');
        }

        return $this->render('workout/create.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/workout', name: 'show_workouts')]
    public function show(WorkoutService $workoutService): Response
    {
        $user = $this->getUser();
        $workouts = null;
        if($user) {
            if (in_array('ROLE_TRAINER', $user->getRoles())) {
                $workouts = $workoutService->findAllWorkouts();
            } else {
                $workouts = $workoutService->findByUser($user);
            }
        }

        return $this->render('workout/show.html.twig', [
            'workouts' => $workouts]);
    }

    #[Route('/workout/delete/{id}', name: 'delete_workout', methods: ['DELETE'])]
    public function destroy(Request $request, WorkoutService $workoutService, int $id)
    {
        $workoutService->deleteWorkout($workoutService->getWorkoutById($id)->getId());
        return $this->redirectToRoute('show_workouts');
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('show_workouts');
    }
}
