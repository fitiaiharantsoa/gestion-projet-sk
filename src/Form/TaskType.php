<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => '📝 Titre'
            ])
            ->add('description', null, [
                'label' => '📄 Description'
            ])
            ->add('dateEcheance', DateTimeType::class, [
                'label' => '📅 Deadline',
                'widget' => 'single_text'
            ])
            // ->add('assigne', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => fn(User $user) => $user->getNom(),
            //     'label' => '👤 Assigné',
            // ])
            // ->add('demandeur', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => fn(User $user) => $user->getNom(),
            //     'label' => '✉️ Demandeur',
            // ])
            ->add('statut', null, [
                'label' => '📌 Statut'
            ])
            ->add('priorite')
            ->add('progression')
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'titre',
                'label' => 'Projet lié'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
