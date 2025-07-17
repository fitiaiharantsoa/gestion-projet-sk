<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('type', TextType::class, [
                'label' => 'Type de fichier',
            ])
            ->add('dateUpload', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de téléchargement',
            ])
            ->add('projet', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'titre',
                'label' => 'Projet associé',
                'placeholder' => 'Choisissez un projet',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectFile::class,
        ]);
    }
}
