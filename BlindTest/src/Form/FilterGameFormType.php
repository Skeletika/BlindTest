<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
class FilterGameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                ])
            ->add('types', ChoiceType::class, [
                'label' => 'Types de questions',
                'choices' => [
                    'Texte' => 'texte',
                    'Image' => 'image',
                    'Vidéo' => 'video',
                    'Audio' => 'audio',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('timer', RangeType::class, [
                'label' => 'Temps par question',
                'attr' => [
                    'value' => 15,
                    'min' => 7,
                    'max' => 25
                ],
            ])
            ->add('nb_questions', RangeType::class, [
                'label' => 'Nombre de questions',
                'attr' => [
                    'value' => 20,
                    'min' => 2,
                    'max' => 100
                ],
            ])
            
            ->add('save', SubmitType::class, [
                    'label' => 'Jouer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // option form
        ]);
    }
}
