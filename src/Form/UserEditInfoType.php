<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserEditInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'attr' => [
                        'placeholder' => 'Saisissez votre prénom'
                    ]
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Nom de famille',
                    'attr' => [
                        'placeholder' => 'Saisissez votre nom de famille'
                    ]
                ]
            )
            ->add(
                'email',
                EmailType::class, 
                [
                    'label' => 'Adresse Email',
                    'attr' => [
                        'placeholder' => 'Saisissez votre adresse Email'
                    ]
                ]
            )
            ->add(
                'gender',
                ChoiceType::class,
                [
                    'choices' => [
                        'Femme' => User::FEMMME,
                        'Homme' => User::HOMME,
                        'Autre' => User::AUTRE,
                    ],
                    'label' => 'Genre',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
