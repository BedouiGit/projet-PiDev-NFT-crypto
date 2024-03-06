<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
            ->add('first_name', TextType::class, [
                'label' => 'First Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Last Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter an email address',
                        ]),
                        new Email([
                            'message' => 'The email "{{ value }}" is not a valid email.',
                        ]),
                    ],
                ])
                // ->add('roles', ChoiceType::class, [
                //     'label' => 'Your Role',
                //     'choices' => [
                //         'Admin' => 'ROLE_ADMIN',
                //         'User' => 'ROLE_USER',
                //     ],
                //     'expanded' => true, // Renders as a navbar
                //     'multiple' => false, // Allow only one role selection
                //     'required' => true,
                // ])   
            ->add('address', TextType::class, [
                    'label' => 'Address',
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter your address',
                        ]),
                    ],
                ])
            ->add('tel', TextType::class, [
                    'label' => 'Telephone Number',
                    'required' => true, // You can set it to false if tel is not mandatory
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter your telephone number',
                        ]),
                        new Regex([
                            'pattern' => '/^\+?\d{1,}$/',
                            'message' => 'Please enter a valid telephone number',
                        ]),
                    ],
                ])
            ->add('age', IntegerType::class, [
                    'label' => 'Age',
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter your age',
                        ]),
                        new GreaterThan([
                            'value' => 0,
                            'message' => 'Please enter a positive age',
                        ]),
                    ],
                ])
            ->add('gender', ChoiceType::class, [
                    'choices' => [
                        'Male' => 'male',
                        'Female' => 'female',
                    ],
                    'expanded' => true, // Renders as a navbar
                ])
            // ->add('agreeTerms', CheckboxType::class, [
            //         'mapped' => false,
            //         'constraints' => [
            //             new IsTrue([
            //                 'message' => 'You should agree to our terms.',
            //             ]),
            //         ],
            //     ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add("captcha", ReCaptchaType::class)
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
