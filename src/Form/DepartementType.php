<?php

namespace App\Form;

use App\Controller\BUController;
use App\Entity\BU;
use App\Entity\Departement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartementType extends AbstractType
{
    private function AfficherRole(Array $role){
        switch ($role[0]) {
            case 'ROLE_PDG':
                return '(PDG)';
            case 'ROLE_BU':
                return '(Chef de BU)';
            default:
                return '(Collaborateur)';
        }
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('chef', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getPrenom() . ' ' . $user->getNom() .' '. $this->AfficherRole($user->getRoles());
                },
            ])
            ->add('bu', EntityType::class, [
                'label'=>'BU du département',
                'choice_label' => 'nom',
                'class' => BU::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Departement::class,
        ]);
    }
}
