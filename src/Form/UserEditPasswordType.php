<?php

namespace App\Form;

use App\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserEditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'oldPassword', PasswordType::class,
                [
                    'label' => 'Mot de passe actuel',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Saisissez votre mot de passe actuel',
                    ]
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Nouveau mot de passe',
                        'attr' => [
                            'placeholder' => 'Saisissez votre nouveau mot de passe'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirmez votre nouveau mot de passe',
                        'attr' => [
                            'placeholder' => 'Confirmez votre nouveau mot de passe'
                        ]
                        ],
                    'invalid_message' => 'Les mots de passe doivent correspondre'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }
}
