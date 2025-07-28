<?php

namespace App\Form;

use App\Entity\ProjectFile;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ texte pour indiquer le type du fichier (ex: "Documentation", "Rapport", etc.)
            ->add('type', TextType::class, [
                'label' => 'Type de fichier',
                'required' => true,
            ])

            // Champ de date pour la date d’upload
            ->add('dateUpload', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de téléchargement',
                'required' => true,
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
                'placeholder' => 'Choisir un projet',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectFile::class,
        ]);
    }
}
