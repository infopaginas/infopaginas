<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ResetPasswordType
 * @package Domain\SiteBundle\Form
 */
class ResetPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'label' => 'Password',
            'first_options'  => [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Password',
                ],
            ],
            'second_options' => [
                'label' => 'Confirm Password',
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Confirm Password',
                ],
            ],
            'invalid_message' => 'fos_user.password.mismatch',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 6, 'minMessage' => 'user.password.min_length']),
            ],
        ])
        ;
    }
}
