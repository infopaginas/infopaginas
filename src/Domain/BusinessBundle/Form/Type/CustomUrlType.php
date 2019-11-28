<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\VO\Url;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomUrlType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'required' => true,
                'label' => 'Url',
            ])
            ->add('relNoFollow', CheckboxType::class, [
                'label' => 'No Follow',
                'data' => true,
            ])
            ->add('relNoOpener', CheckboxType::class, [
                'label' => 'No Opener',
                'data' => true,
            ])
            ->add('relNoReferrer', CheckboxType::class, [
                'label' => 'No Referrer',
                'data' => true,
            ])
            ->add('relSponsored', CheckboxType::class, [
                'label' => 'Sponsored',
                'data' => false,
            ])
            ->add('relUGC', CheckboxType::class, [
                'label' => 'User Generated Content',
                'data' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Url::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_custom_url_type';
    }
}
