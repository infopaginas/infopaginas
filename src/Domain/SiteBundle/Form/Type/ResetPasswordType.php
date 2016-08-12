<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 6, 'minMessage' => 'user.password.min_length']),
            ],
        ])
        ;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'domain_site_reset_password';
    }
}
