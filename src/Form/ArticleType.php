<?php

namespace App\Form;

use App\Entity\Article;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control ',
                        'minlength' => '2',
                        'maxlength' => '255'

                    ],

                    'label' => 'Titre',
                    'label_attr' =>
                    [
                        'class' => 'form-label mt-4'
                    ],

                    'constraints' =>
                    [
                        new Assert\Length(['min' => 2, 'max' => 255]),
                        new Assert\NotBlank()
                    ]

                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => '2',
                        'maxlength' => '10000'
                    ],

                    'label' => 'Description',
                    'label_attr' =>
                    [
                        'class' => 'form-label mt-4'
                    ],

                    'constraints' =>
                    [
                        new Assert\Length(['min' => 2, 'max' => 10000]),
                        new Assert\NotBlank()
                    ]

                ]
            )
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image de l\'article',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false
            ])
            ->add(
                'submit',
                SubmitType::class,
                [

                    'attr' =>

                    [
                        'class' => "btn btn-success mt-4"
                    ],

                    'label' => 'Enregister l\'article',
                    


                ]


                );
         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
