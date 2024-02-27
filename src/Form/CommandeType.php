<?php

namespace App\Form;

use App\Entity\Commande;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new Email([
                    'message' => 'The email "{{ value }}" is not a valid email.',
                ]),
                new NotBlank([
                    'message' => 'The email field cannot be empty.',
                ]),
            ],
        ])
        ->add('Wallet', TypeTextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'The wallet field cannot be empty.',
                ]),
                new Length([
                    'min' => 26,
                    'max' => 35,
                    'minMessage' => 'Your wallet address must be at least {{ limit }} characters long',
                    'maxMessage' => 'Your wallet address cannot be longer than {{ limit }} characters',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'nft_price' => null,
            'csrf_protection' => false,
        ]);

        
}

}
