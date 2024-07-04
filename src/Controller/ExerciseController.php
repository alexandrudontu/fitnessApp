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

    #[Route('/exercise', name: 'show_exercises')]
    public function show(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAll();

        return $this->render('exercise/show.html.twig', [
            'exercises' => $exercises]);
    }

    #[Route('/exercise/{id}', name: 'edit_exercise', methods: ['GET', 'PUT'])]
    public function update(Request $request, ExerciseService $exerciseService, $id): Response
    {
        $exercise = $exerciseService->getExerciseById($id);
        if (!$exercise) {
            throw $this->createNotFoundException('No exercise found for id ' . $id);
        }

        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = $form->getData();
            $result = $exerciseService->updateExercise($exercise);

            if (!$result['success']) {
                $this->addFlash('error', $result['message']);
                return $this->redirectToRoute('edit_exercise', ['id' => $id]);
            }

            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('show_exercises');
        }

        return $this->render('exercise/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/exercise/{id}', name: 'delete_exercise', methods: ['DELETE'])]
    public function destroy(Request $request, ExerciseService $exerciseService, int $id)
    {
        $exerciseService->deleteExercise($exerciseService->getExerciseById($id)->getId());
        return $this->redirectToRoute('show_exercises');
    }
}
