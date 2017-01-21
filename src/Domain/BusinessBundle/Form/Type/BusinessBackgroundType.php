<?php

namespace Domain\BusinessBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessBackgroundType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessBackgroundType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class'     => 'form-control',
                    'readonly'  => true,
                ],
                'label' => 'Name',
            ])
            ->add('id', HiddenType::class, [])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Oxa\Sonata\MediaBundle\Entity\Media',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_background_type';
    }
}
