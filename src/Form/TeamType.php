<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TeamType extends AbstractType
{

    private $token;
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
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
                'time',
                IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 1440,
                    ],
                    'required' => false,
                    'label' => 'Temps (en minutes)',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(1441)
                    ]
                ]
            )
            ->add(
                'NbPeople',
                IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 50
                    ],
                    'required' => false,
                    'label' => 'Nombre de personnes',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(51)
                    ]
                ]
            )
            ->add(
                'difficulty',
                RangeType::class,
                [
                    'attr' => [
                        'class' => 'form-range',
                        'min' => 1,
                        'max' => 5
                    ],
                    'required' => false,
                    'label' => 'Difficulté',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                    ]
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 5
                    ],
                    'label' => 'Description',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\NotBlank()
                    ]
                ]
            )
            ->add(
                'isFavorite',
                CheckboxType::class,
                [
                    'attr' => [
                        'class' => 'form-check-input',
                    ],
                    'required' => false,
                    'label' => 'Favoris ? ',
                    'label_attr' => [
                        'class' => 'form-check-label'
                    ],
                    'constraints' => [
                        new Assert\NotNull()
                    ]
                ]
            )

            ->add('imageFile', VichImageType::class, [
                'label' => 'Image de la Team',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false
            ])

            ->add(
                'isPublic',
                CheckboxType::class,
                [
                    'attr' => [
                        'class' => 'form-check-input',
                    ],
                    'required' => false,
                    'label' => 'Rendre public ? ',
                    'label_attr' => [
                        'class' => 'form-check-label'
                    ],
                    'constraints' => [
                        new Assert\NotNull()
                    ]
                ]
            )
            ->add(
                'article',
                EntityType::class,
                [
                    'class' => Article::class,
                    'query_builder' => function (ArticleRepository $r) {
                        return $r->createQueryBuilder('i')
                            ->where('i.user = :user')
                            ->orderBy('i.title', 'ASC')
                            ->setParameter('user', $this->token->getToken()->getUser());
                    },
                    'label' => 'Choix des exercices',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'choice_label' => 'title',
                    'multiple' => true,
                    'expanded' => true,


                ]

            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-success mt-4'
                    ],
                    'label' => 'Créer une Team'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
