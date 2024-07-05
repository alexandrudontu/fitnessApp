<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Workout;
use App\Form\WorkoutType;
use App\Repository\MuscleGroupRepository;
use App\Repository\WorkoutRepository;
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

            return $this->redirectToRoute('app_workout');
        }

        return $this->render('workout/create.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/workout', name: 'show_workouts')]
    public function show(WorkoutService $workoutService): Response
    {
        $user = $this->getUser();
        $workouts = $workoutService->findByUser($user);

        return $this->render('workout/show.html.twig', [
            'workouts' => $workouts]);
    }
}
