<?php

namespace App\Form;

use App\Entity\Actualite;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class ActualiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 5, 'max' => 255]),
                ],
            ])
            ->add('contenu', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10]),
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Digital Art' => 'Digital Art',
                    'Collectibles' => 'Collectibles',
                    'Music' => 'Music',
                    'Gaming' => 'Gaming',
                    'Metaverse' => 'Metaverse',
                    'Sports' => 'Sports',
                    'Virtual Real Estate' => 'Virtual Real Estate',
                    'Crypto Art' => 'Crypto Art',
                    'Fashion' => 'Fashion',
                    'Domain Names' => 'Domain Names',
                    'NFT Platforms' => 'NFT Platforms',
                    'Memorabilia' => 'Memorabilia',
                    'Other' => 'Other',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('image_url', FileType::class, [
                'label' => 'Photo (JPEG or PNG file)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '3000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ])
                ],
            ]);
           ;
           
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actualite::class,
        ]);
    }
}
