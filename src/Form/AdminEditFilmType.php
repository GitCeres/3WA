<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminEditFilmType extends AbstractType
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
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label' => "Catégorie du film",
                    'attr' => [
                        'placeholder' => 'Saisissez le nom du réalisateur du film'
                    ]
                ]
            )
            ->add(
                'synopsis',
                TextareaType::class,
                [
                    'label' => 'Synopsis du film',
                    'attr' => [
                        'placeholder' => 'Saisissez le synopsis du film',
                        'rows' => 10
                    ]
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => "Affiche du film",
                    'required' => false,
                    'allow_delete' => false,
                    'download_link' => false,
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
