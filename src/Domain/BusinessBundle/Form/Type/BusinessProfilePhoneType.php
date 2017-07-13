<?php

namespace Domain\BusinessBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneNumber;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class BusinessProfilePhoneType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessProfilePhoneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class, [
                'attr'  => [
                    'class' => 'form-control',
                    'placeholder' => '(787) 594-7273',
                ],
                'constraints' => [
                    new Length([
                        'max' => 10
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9-]*$/',
                        'message' => 'business_profile.phone.digit_dash',
                    ]),
                    new NotBlank(),
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Domain\BusinessBundle\Entity\BusinessProfilePhone',
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_profile_phone_type';
    }
}
