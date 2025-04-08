<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez entrer une adresse e-mail']
                    ),
                    new Regex(
                        "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
                        'Veuillez entrer une adresse e-mail valide'
                    )
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez entrer un nom']
                    )
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez entrer un prénom']
                    )
                ]
            ])
            ->add('contrat', TextType::class, [
                'label' => 'Statut',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez entrer un type de contrat']
                    )
                ]
            ])
            ->add('contratStartDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'entrée',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez entrer une date d\'entrée']
                    )
                ]
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
