<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Author cannot be blank']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Author must be at least {{ limit }} characters long',
                        'maxMessage' => 'Author cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('contenu', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Content cannot be blank']),
                    new Length([
                        'min' => 6,
                        'max' => 1000,
                        'minMessage' => 'Content must be at least {{ limit }} characters long',
                        'maxMessage' => 'Content cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
