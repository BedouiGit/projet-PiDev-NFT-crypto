<?php

namespace App\Form;

use App\Entity\NFT;
use App\Entity\Projets;
use PHPUnit\Framework\Constraint\LessThan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThan as ConstraintsLessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class NFTType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            // Apply the NotBlank constraint to ensure the field is not empty
            'constraints' => [
                new NotBlank([
                    'message' => 'The name cannot be blank.',
                ]),
            ],
        ])
            ->add('price', NumberType::class, [
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 10,
                        'message' => 'The price must be more than 10.',
                    ]),
                    new ConstraintsLessThan([
                        'value' => 666,
                        'message' => 'The price must be less than 666.',
                    ]),
                ],
            ])
            ->add('project', EntityType::class, [
                'class' => Projets::class,
                'choice_label' => 'nom',               
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Sell' => 'sell',
                    'Private' => 'private',
                    'Auction' => 'auction',
                ],
                'constraints' => [
                    new Choice([
                        'choices' => ['sell', 'private', 'auction'],
                        'message' => 'Please select a valid status.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo (JPEG or PNG file)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ])
                ],
                'attr' => [
                    'id' => 'photoURL',
                    'class' => 'custom-file-input',
                    'onchange' => 'previewImage(this)'
                ]

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NFT::class,
        ]);
    }
}
