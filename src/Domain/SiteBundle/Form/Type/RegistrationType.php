<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Domain\SiteBundle\Validator\Constraints\ContainsEmailExpanded;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Email',
                ],
                'constraints' => [
                    new NotBlank(),
                    new ContainsEmailExpanded(),
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
                'invalid_message' => 'fos_user.password.mismatch',
                'constraints' => [
                    new Length(['min' => 6, 'minMessage' => 'user.password.min_length']),
                    new NotBlank(),
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_site_registration';
    }
}
