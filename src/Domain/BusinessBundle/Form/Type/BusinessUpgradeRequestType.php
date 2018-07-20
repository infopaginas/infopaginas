<?php

namespace Domain\BusinessBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class BusinessReviewType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessUpgradeRequestType extends AbstractType
{
    const TIME_CHOICES = [
        '9-1' => '9 a.m. - 1 p.m.',
        '1-6' =>'1 p.m. - 6 p.m.',
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class, [
                'label'       => 'user_profile.label.your_phone',
                'attr'        => [
                    'class' => 'form-control review',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('time', ChoiceType::class, [
                'label'   => 'user_profile.label.when_call',
                'attr'    => [
                    'class' => 'form-control review',
                ],
                'choices' => self::TIME_CHOICES,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_upgrade_request_type';
    }
}
