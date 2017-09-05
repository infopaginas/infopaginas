<?php

namespace Domain\SiteBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\SiteBundle\Validator\Constraints\ConstraintUrlExpanded;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class UserProfileType
 * @package Domain\SiteBundle\Form\Type
 */
class UserProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Catalá',
                ],
                'label' => 'First Name',
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Joyeros',
                ],
                'label' => 'Last Name',
            ])
            ->add('location', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Puerto Rico',
                ],
                'label' => 'Location',
            ])
            ->add('advertiserId', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '11111111',
                ],
                'label' => 'Advertiser Id',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '787-594-7273',
                ],
                'label' => 'Phone Number',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => BusinessProfilePhone::REGEX_PHONE_PATTERN,
                        'message' => 'business_profile.phone.invalid',
                    ]),
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oxa\Sonata\UserBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'domain_site_user_profile';
    }
}
