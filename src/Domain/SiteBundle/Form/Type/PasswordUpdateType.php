<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PasswordUpdateType
 * @package Domain\SiteBundle\Form\Type
 */
class PasswordUpdateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Old Password',
                ],
                'constraints' => [
                    new UserPassword(),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'New Password',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Confirm New Password',
                    ],
                ],
                'invalid_message' => 'fos_user.password.mismatch',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6, 'minMessage' => 'user.password.min_length']),
                ],
            ]);
    }
}
