<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use App\Form\ExerciseType;
use App\Form\MuscleGroupType;
use App\Repository\ExerciseRepository;
use App\Repository\UserRepository;
use App\Service\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExerciseController extends AbstractController
{
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
            $result = $exerciseService->addExercise($exercise);

            if (!$result['success']) {
                $this->addFlash('error', $result['message']);
                return $this->redirectToRoute('app_exercise');
            }

            // ... perform some action, such as saving the task to the database
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_exercise');
        }

        return $this->render('exercise/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/exercises', name: 'show_exercises')]
    public function show(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAll();

        if (!$exercises) {
            throw $this->createNotFoundException('No users found');
        }

        return $this->render('exercise/show.html.twig', [
            'exercises' => $exercises]);
    }
}
