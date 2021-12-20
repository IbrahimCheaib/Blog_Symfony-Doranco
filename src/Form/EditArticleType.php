<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title',TextType::class, [
            'label' => 'Titre',
            'required' => true,
            'attr' => ['placeholder' => 'Entrez un titre']
        ])
        ->add('subtitle',TextType::class, [
            'label' => 'Soustitre',
            'required' => true,
            'attr' => ['placeholder' => 'Entrez un soustitre']
        ])
        ->add('description',TextareaType::class, [
            'label' => 'Description',
            'required' => true,
            'attr' => ['placeholder' => 'Entrez unse description']
        ])
        ->add('picture',TextType::class, [
            'label' => 'Photo',
            'required' => true,
            'attr' => ['placeholder' => 'Entrez une illustration']
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Modifier',
            'attr' => ['class' => 'btn btn-warning d-block mx-auto col-4 my-3']
        ]);
    
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
