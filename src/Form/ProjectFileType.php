<?php

namespace App\Form;

use App\Entity\ProjectFile;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProjectFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', null, [
                'label' => 'Nom de fichier',
                'attr' => ['placeholder' => 'Entrez le nom du fichier', 'class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'mb-3']
            ])

            // Champ de sélection du projet associé
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'titre',
                'label' => 'Projet associé',
                'placeholder' => 'Choisissez un projet',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'row_attr' => ['class' => 'mb-3']
            ])
            ->add('filepath', FileType::class, [
                'label' => 'Fichier à importer',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new File(
                        maxSize: '10240k',
                        extensions: ["pdf", 'doc', 'docx', 'txt', 'csv', 'xlsx'],
                        extensionsMessage: 'Le fichier n\'est pas valide'
                    )
                ],
                'row_attr' => ['class' => 'mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectFile::class,
        ]);
    }
}
