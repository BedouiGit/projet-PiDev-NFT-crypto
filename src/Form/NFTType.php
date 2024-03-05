<?php

namespace App\Form;

use App\Entity\NFT;
use PHPUnit\Framework\Constraint\LessThan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
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
            ->add('image', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'The name cannot be blank.',
                    ]),
                    new Regex([
                        'pattern' => '/^.+\/.+(\.png|\.jpg|\.jpeg)$/',
                        'message' => 'The image path must contain "/" and end with one of the following extensions: .png, .jpg, .jpeg.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NFT::class,
        ]);
    }
}
