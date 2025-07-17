<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'PDG' => 'ROLE_PDG',
                    'Chef de BU' => 'ROLE_BU',
                    'Collaborateur' => 'ROLE_USER',
                ],
                'expanded' => true,   // checkboxes
                'multiple' => true,   // plusieurs rôles possibles
                'required' => true,
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Utilisateur vérifié ?',
                'required' => false,
            ])
            ->add('isEmailAuthEnabled', CheckboxType::class, [
                'label' => 'Activer l’authentification 2FA par email',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
