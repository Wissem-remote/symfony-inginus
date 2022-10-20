<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email', EmailType::class, [
            'constraints' => [
                new Regex([
                    'pattern' => '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$/',
                    'message' => "Votre Email est invalide ",
                ])
            ],
        ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter les terms',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => "S'il vous plait entrer votre mot passe.",
                    ]),
                    new Regex([
                        'pattern' => '/(?=[0-9a-zA-Z.*]+$)^(?=.*[A-Z])(?=.{6,}).*$/',
                        'message' => "Votre mot de passe doit contenir 6 caractères et une majuscule ",
                    ])
                    // new Length([
                    //     'min' => 6,
                    //     'minMessage' => 'Votre mot passe doit avoir au moin 6 lettres et une majuscule.',
                    //     // max length allowed by Symfony for security reasons
                    //     'max' => 4096,
                    // ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
