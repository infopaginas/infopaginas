<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 28.06.16
 * Time: 18:10
 */

namespace Domain\SiteBundle\Form\Type;

use Domain\SiteBundle\Validator\Constraints\ConstraintUrlExpanded;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Url;

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
            ->add('twitterURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://twitter.com/user',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Twitter',
            ])
            ->add('facebookURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://www.facebook.com/user',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Facebook',
            ])
            ->add('googleURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://plus.google.com/user',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Google Plus',
            ])
            ->add('youtubeURL', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new ConstraintUrlExpanded(),
                    new Length(['max' => 100]),
                ],
                'label' => 'Youtube',
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
