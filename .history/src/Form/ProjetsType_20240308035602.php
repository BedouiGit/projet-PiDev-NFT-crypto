<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Projets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProjetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('nom', null, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 255]),
            ],
        ])
        ->add('Description', null, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 10, 'max' => 1000]),
            ],
        ])
        ->add('WalletAddress', null, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 10, 'max' => 100]),
            ],
        ])
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
                ]),
                new NotBlank(),
            ],
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'nom',
            'constraints' => [
                new NotBlank(),
            ],
        ])
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
        ]);
    }
}
