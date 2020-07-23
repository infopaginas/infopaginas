<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'business-phone-type',
                ],
                'choices'  => BusinessProfilePhone::getTypes(),
                'multiple' => false,
                'required' => true,
                'choice_translation_domain' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => BusinessProfilePhone::REGEX_PHONE_PATTERN,
                        'message' => 'business_profile.phone.digit_dash',
                    ]),
                ],
                'required' => true,
            ])
            ->add('extension', TextType::class, [
                'label' => 'Extension',
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
}
