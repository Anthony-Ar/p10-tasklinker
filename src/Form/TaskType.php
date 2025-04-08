<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la tâche',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Veuillez renseigner un titre']
                    ),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('deadline', null, [
                'widget' => 'single_text',
                'label' => 'Date',
                'required' => false
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label' => 'Statut'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user): string {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'choices' => $options['project']->getUser(),
                'label' => 'Membre',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project' => null
        ]);
    }
}
