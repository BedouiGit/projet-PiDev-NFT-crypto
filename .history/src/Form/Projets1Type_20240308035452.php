<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Projets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Length;


class Projets1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name.',
                    ]),
                ],
            ])
            ->add('Description', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description.',
                    ]),
                ],
            ])
            ->add('walletAddress', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10, 'max' => 100]),
                ],
            ])
            ->add('DateDeCreation')
            ->add('photoURL', FileType::class, [
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
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a category.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
        ]);
    }
}
