<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 13.07.16
 * Time: 20:50
 */

namespace Domain\BusinessBundle\Form\Type;

use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessGalleryType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessGalleryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', EntityHiddenType::class, [
                'class' => 'Oxa\Sonata\MediaBundle\Entity\Media',
                'attr' => ['class' => 'hidden-media'],
            ])
            ->add('isPrimary', CheckboxType::class, [
                'attr' => [
                    'class' => 'is-primary',
                ],
                'label' => 'Primary'
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control textarea-control',
                    'rows' => 3,
                ],
                'label' => 'Description',
            ])
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control select-control',
                ],
                'choices' => [
                    OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO => 'Logo',
                    OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES => 'Photo',
                    OxaMediaInterface::CONTEXT_BANNER => 'Banner Ad',
                ],
                'expanded' => false,
                'label' => 'Type',
                'multiple' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Domain\BusinessBundle\Entity\Media\BusinessGallery',
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_gallery_type';
    }
}
