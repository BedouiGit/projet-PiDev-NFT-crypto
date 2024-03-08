<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class Category1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'max' => 255]),
            ],
        ])
        ->add('description', TextareaType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 10, 'max' => 1000]),
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
            'attr' => [
                'id' => 'photoURL',
                'class' => 'custom-file-input',
                'onchange' => 'previewImage(this)',
            ],
        ])
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
