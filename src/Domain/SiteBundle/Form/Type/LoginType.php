<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ],
                'block_name' => '_username',
            ])
            ->add('_password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Password',
                ]
            ])
        ;
    }

    public function getName()
    {
        return null;
    }
}
