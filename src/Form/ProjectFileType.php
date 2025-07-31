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
                'label'=>'Nom de fichier',
                'attr' => ['placeholder' => 'Entrez le nom du fichier'],
            ])

            // Champ d’import de fichier depuis l’ordinateur
            ->add('fichier', FileType::class, [
                'label' => 'Fichier à importer',
                'mapped' => false, // Ne mappe pas directement à l'entité
                'required' => true,
            ])

            // Champ de sélection du projet associé
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'titre',
                'label' => 'Projet associé',
                'placeholder' => 'Choisissez un projet',
            ])
            ->add('project_file', FileType::class, [
                'label'=>'Upload de fichier',
                'required' => true,
                'mapped'=>false,
                'constraints'=>[
                    new File(
                        maxSize: '10240k',
                        extensions:["pdf", 'doc', 'docx', 'txt', 'csv', 'xlsx'],
                        extensionsMessage:'Le fichier n\'est pas valide'
                    )
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectFile::class,
        ]);
    }
}
