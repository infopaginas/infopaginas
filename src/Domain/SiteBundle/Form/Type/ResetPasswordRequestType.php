<?php

namespace Domain\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ResetPasswordRequestType
 * @package Domain\SiteBundle\Form
 */
class ResetPasswordRequestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Email',
            ],
            'constraints' => [
                new NotBlank(),
                new Email(),
            ],
        ]);
    }
}
