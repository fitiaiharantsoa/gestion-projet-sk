<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ProjectType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('titre', null,[
                'attr'=>['class'=>'form-control'],
                'label'=> 'Titre',
                'label_attr'=>[
                    'class'=>''
                ],
                'row_attr'=>[
                    'class'=>'mb-2',
                    'placeholder'=>'Titre'
                ],
               
            ])
            ->add('description', null, [
                'label_attr'=>['class'=>'form-label mt-2'],
                'attr'=>['class'=>'form-control mb-2'],
                'required'=>true
            ])
            ->add('bu', ChoiceType::class, [
                'label'=>"BU",
                'choices'=>[
                    "Yitro consulting"=>"yitro-consulting",
                    "Sk travel"=>"sk_travel"
                ],
                'required'=>true,
                'label_attr'=>['class'=>'form-label mt-2'],
                'attr'=>['class'=>'form-select ']
            ])
            ->add('statut', ChoiceType::class, [
                'label'=>'Status',
                'label_attr'=>['class'=>'form-label mt-2'],
                'attr'=>['class'=>'form-select'],
                'choices'=>[
                    'A faire'=>'à faire',
                    'En cours'=>'en cours',
                    'Bloqué'=>'bloqué',
                    'Terminé'=>'terminé'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'label_attr'=>['class'=> 'form-label mt-2'],
                'attr'=>['class'=> 'form-control'],
                'constraints'=>[
                    new GreaterThanOrEqual([
                        'value'=>'today',
                        'message'=>'la date doit être superieur ou égal a aujourd\'hui'
                    ])
                ]
            ])
            ->add('dateFin', DateType::class, [
                'label_attr'=>['class'=> 'form-label mt-2'],
                'attr'=>['class'=> 'form-control']
            ])
            // ->add('statut', ChoiceType::class, [
            //     'label'=> 'Status',
            //     'choices'=>[
            //         "à faire"=>"a_faire",
            //         "en cours"=>"en_cours",
            //         "bloquée"=>"bloque",
            //         "terminée"=>"terminée"
            //     ],
            //     'required'=>true,
            //     'attr'=>['class'=>'form-select'],
            //     'label_attr'=>['class'=>'form-label mt-2']
            // ])
            ->add('categorie', ChoiceType::class, [
                'label'=>'Catégorie',
                "choices"=>[
                    'Haut'=>'haut',
                    'Moyenne'=>'moyenne',
                    'Bas'=>'bas'
                ],
                'required'=>true,
                'label_attr'=>['class'=>'form-label mt-2'],
                'attr'=>['class'=>'form-select']
            ])
            ->add('departement', EntityType::class,  [
                'class'=>Departement::class,
                'choice_label'=>'nom',
                'label_attr'=>['class'=>'form-label mt-2'],
                'attr'=>['class'=>'form-select']
            ])
            ->add('responsable', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user){
                    return sprintf('%s %s', $user->getNom(), $user->getPrenom());
                },
                'required'=>true,
                'label_attr'=>['class'=>'form-label mt-2'],
                'attr'=>['class'=>'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
