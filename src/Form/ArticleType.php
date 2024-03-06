<?php

namespace App\Form;

use App\Entity\Article;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ArticleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
      
    
        ->add('titre', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le titre ne peut pas être vide',
                ]),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => 'Le titre doit avoir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le titre doit avoir au plus {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('contenu', TextareaType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le contenu ne peut pas être vide',
                ]),
                new Length([
                    'min' => 10,
                    
                    'minMessage' => 'Le contenu doit avoir au moins {{ limit }} caractères',
                    
                ]),
            ],
    ],
        )
        ->add('auteur', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'auteur ne peut pas être vide',
                ]),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'minMessage' => 'L\'auteur doit avoir au moins {{ limit }} caractères',
                    'maxMessage' => 'L\'auteur doit avoir au plus {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('tags')
        ->add('photo', FileType::class, [
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
        ;
      
}
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
