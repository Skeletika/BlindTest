<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Questions;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AddQuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Texte' => 'texte',
                    'Image' => 'image',
                    'Audio' => 'audio',
                    'Vidéo' => 'video',
                ]
            ])
            ->add('question')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
            ])
            ->add('clue', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Indice',
            ])
            ->add('path', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Ajouter un fichier'
            ])
            ->add('answer', CollectionType::class, [
                'mapped' => false,
                'entry_type' => TextType::class,
                'label' => 'Réponses possibles',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'attr' => [
                    'class' => 'tags-collection'
                ],
            ])
            // ->add('answer', TextType::class, [
            //     'mapped' => false,
            //     'label' => 'Réponse possible',
            // ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Questions::class,
        ]);
    }
}
