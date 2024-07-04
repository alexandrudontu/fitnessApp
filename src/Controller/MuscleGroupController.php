<?php

namespace App\Controller;

use App\Entity\MuscleGroup;
use App\Form\MuscleGroupType;
use App\Form\UserType;
use App\Repository\ExerciseRepository;
use App\Repository\MuscleGroupRepository;
use App\Repository\UserRepository;
use App\Service\MuscleGroupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MuscleGroupController extends AbstractController
{
    #[Route('/muscle/group/create', name: 'app_muscle_group')]
    public function create(Request $request, MuscleGroupService $muscleGroupService): Response
    {
        $muscleGroup = new MuscleGroup();

        $form = $this->createForm(MuscleGroupType::class, $muscleGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $muscleGroup = $form->getData();
            $result = $muscleGroupService->addMuscleGroup($muscleGroup);

            if (!$result['success']) {
                $this->addFlash('error', $result['message']);
                return $this->redirectToRoute('app_muscle_group');
            }

            // ... perform some action, such as saving the task to the database
            $this->addFlash('success', $result['message']);
            return $this->redirectToRoute('app_muscle_group');
        }

        return $this->render('muscle_group/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/muscle/group', name: 'show_muscle_groups')]
    public function show(MuscleGroupRepository $muscleGroupRepository): Response
    {
        $muscleGroups = $muscleGroupRepository->findAll();

        return $this->render('muscle_group/show.html.twig', [
            'muscleGroups' => $muscleGroups]);
    }
}
