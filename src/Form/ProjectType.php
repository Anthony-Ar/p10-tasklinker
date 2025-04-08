<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre du projet',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez renseigner un titre pour votre projet.']
                    ),
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'choice_label' => function (User $user): string {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'label' => 'Inviter des membres',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
