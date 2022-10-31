<?php

namespace App\Form;

use App\Entity\Film;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Nom du film',
                    'attr' => [
                        'placeholder' => 'Saisissez le nom du film'
                    ]
                ]
            )
            ->add(
                'synopsis',
                TextareaType::class,
                [
                    'label' => 'Synopsis du film',
                    'attr' => [
                        'placeholder' => 'Saisissez le synopsis du film'
                    ]
                ]
            )
            ->add(
                'director',
                TextType::class,
                [
                    'label' => 'Réalisateur du film',
                    'attr' => [
                        'placeholder' => 'Saisissez le nom du réalisateur du film'
                    ]
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'choices' => [
                        'Action' => Film::ACTION,
                        'Science Fiction' => Film::SF,
                        'Fantastique' => Film::FANTASTIQUE,
                        'Horreur' => Film::HORREUR,
                        'Comédie' => Film::COMEDIE,
                    ],
                    'label' => 'Catégorie du film'
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => "Sélectionnez l'affiche du film"
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
