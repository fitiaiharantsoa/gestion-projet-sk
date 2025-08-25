<?php

namespace App\Form;

use App\Entity\BU;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BUType extends AbstractType
{   
    private $userListe = [];
    // private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository){
        $users = $userRepository->findAll();
        foreach ($users as $key => $value) {
            if ($value->getRoles()[0] == 'ROLE_BU' || $value->getRoles()[0] == 'ROLE_PDG') {
                $this->userListe[] = $value;
            }
        }
    } 

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'attr'=>['class'=>'form-control'],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'row_attr'=>['class'=>'mb-3']
            ])
            ->add('responsable', EntityType::class, [
                'class' => User::class,
                // 'choice_label' => fn(User $user)=> $user->getFullName() ,
                'choices'=>$this->userListe,
                'attr'=>['class'=>'form-select'],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'row_attr'=>['class'=>'mb-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BU::class,
        ]);
    }
}
