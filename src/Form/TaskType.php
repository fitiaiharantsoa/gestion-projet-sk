<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    private $listUserToassigne = [];

    public function __construct(UserRepository $userRepository)
    {
        $listusers = $userRepository->findAll();
        foreach ($listusers as $key => $value) {
            if ($value->getRoles()[0] != 'ROLE_PDG') {
                $this->listUserToassigne[] = $value->getNom();
            }
        }
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=>'test'
                ],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('dateEcheance', DateTimeType::class, [
                'label' => 'Deadline',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('assigne', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getNom() . ' ' . $user->getPrenom(),
                'label' => 'Assigné',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label_attr' => ['class' => 'form-label '],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            // ->add('demandeur', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => fn(User $user) => $user->getNom(),
            //     'label' => 'Demandeur',
            // ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => ['class' => 'form-label '],
                'row_attr' => ['class' => 'form-floating mb-3'],
                'choices'=>[
                    'à faire'=>'à faire',
                    'en cours'=>'en cours',
                    'bloqué'=>'bloqué',
                    'terminé'=>'terminé'
                ]
            ])
            ->add('priorite', ChoiceType::class, [
                
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => ['class' => 'form-label '],
                'row_attr' => ['class' => 'form-floating mb-3'],
                'choices'=>[
                    'Haute'=>'haute',
                    'Moyenne'=>'moyenne',
                    'Basse'=>'basse'
                ]
            ])
            ->add('progression', NumberType::class, [
                'label' => 'Progression',
                'html5'=>true,
                'attr' => ['min' => 0, 'max' => 100, 'type' => 'range', 'id' => 'progression', 'step' => 5, 'class' => 'form-range'],
                'label_attr'=>['class'=>'form-label mb-3'],
                'row_attr'=>['class'=>'form-floating']
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'titre',
                'label' => 'Projet lié',
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>['class'=>'form-label '],
                'row_attr'=>['class'=>'form-floating mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
