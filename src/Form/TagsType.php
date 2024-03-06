<?php

namespace App\Form;

use App\Entity\Tags;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class TagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('nom', TextType::class, [
            'constraints' => [
                 new NotBlank([
                    'message' => 'Le contenu ne peut pas être vide',
                ]),
                new Length([
                    'min' => 10,
                    'max' => 50,
                    'minMessage' => 'Le contenu doit avoir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le contenu doit avoir au plus {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('description', TextType::class, [
            'constraints' => [
                 new NotBlank([
                    'message' => 'Le contenu ne peut pas être vide',
                ]),
                new Length([
                    'min' => 10,
                    'max' => 255,
                    'minMessage' => 'Le contenu doit avoir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le contenu doit avoir au plus {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('phototag', FileType::class, [
                    'label' => 'Photo (JPEG or PNG file)',
                    'mapped' => false,
                    'required' => true,
                    'constraints' =>[ new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                        ])
                    ],
                ])
            ->add('relation')
              
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tags::class,
        ]);
    }
}
