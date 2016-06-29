<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Email',
                ],
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'First Name',
                ],
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Last Name',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => 'password',
                'first_options'  => [
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Password',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Confirm Password',
                    ],
                ],
            ])
            ->add('location', TextType::class, [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Location',
                ],
                'empty_data' => 'San Juan, Puerto Rico',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => 'Oxa\Sonata\UserBundle\Entity\User',
            'validation_groups' => ['Default', 'Registration'],
        ]);
    }

    public function getName()
    {
        return 'domain_site_registration';
    }
}
