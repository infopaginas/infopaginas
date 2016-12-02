<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 28.06.16
 * Time: 18:10
 */

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
                    'placeholder' => '(787) 594-7273',
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
            ->add('twitterURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'twitter.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Twitter',
                'required' => false,
            ])
            ->add('facebookURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'facebook.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Facebook',
                'required' => false,
            ])
            ->add('googleURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'plus.google.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Google Plus',
                'required' => false,
            ])
            ->add('youtubeURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'youtube.example.email.placeholder',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Youtube',
                'required' => false,
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
