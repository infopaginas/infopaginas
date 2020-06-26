<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class BusinessReviewType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessUpgradeRequestType extends AbstractType
{
    public const TIME_CHOICES = [
        '9-1' => '9 a.m. - 1 p.m.',
        '1-6' => '1 p.m. - 6 p.m.',
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
                    new Regex([
                        'pattern' => BusinessProfilePhone::REGEX_PHONE_PATTERN,
                        'message' => 'business_profile.phone.digit_dash',
                    ]),
                ],
            ])
            ->add('time', ChoiceType::class, [
                'label'   => 'user_profile.label.when_call',
                'attr'    => [
                    'class' => 'form-control review',
                ],
                'choices' => array_flip(self::TIME_CHOICES),
            ])
        ;
    }
}
