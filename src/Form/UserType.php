<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
	    $label = $options['type'] === 'edit' ? 'Modifier' : 'Ajouter';
		$c = !($options['type'] === 'edit');

        $builder
	        ->add('name', TextType::class, [
		        'label' => 'Nom',
	        ])
            ->add('email', EmailType::class, [
				'label' => 'Email',
            ])
            ->add('roles', ChoiceType::class, [
				'label' => 'Roles',
	            'multiple' => true,
				'expanded' => true,
	            'choices'=> [
		            'Administrateur' => 'ROLE_ADMIN',
	            ]
            ])
            ->add('password', RepeatedType::class, [
				'type' => PasswordType::class,
	            'first_options'  => ['label' => 'Mot de passe'],
	            'second_options' => ['label' => 'Confirmation mot de passe'],
	            'required' => $options['type'] === 'create',
	            'mapped' => $c,
            ])
            ->add('description', TextareaType::class, [
				'label' => 'Description',
	            'required' => false
            ])

	        ->add('save', SubmitType::class, [
				'label' => $label,
		        'attr' => ['class' => 'btn btn-primary'],
	        ])
        ;

			if ($options['type'] === 'edit') {
				$builder->add('blocked', CheckboxType::class, [
					'label' => 'Bloquer',
					'required' => false,
				]);
			}
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
	        'type' => 'create',
        ]);
    }
}
