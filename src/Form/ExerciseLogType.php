<?php

namespace App\Form;

use App\Entity\Exercise;
use App\Entity\ExerciseLog;
use App\Entity\Workout;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciseLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duration', IntegerType::class, [
                'label' => 'Duration',
                'required' => true,
            ])
            ->add('reps', IntegerType::class, [
                'label' => 'Reps',
                'required' => true,
            ])
            ->add('sets', IntegerType::class, [
                'label' => 'Sets',
                'required' => true,
            ])
            ->add('exercise', EntityType::class, [
                'class' => Exercise::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseLog::class,
        ]);
    }
}
