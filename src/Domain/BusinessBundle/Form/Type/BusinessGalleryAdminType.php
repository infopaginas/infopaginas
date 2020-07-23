<?php

namespace Domain\BusinessBundle\Form\Type;

use Oxa\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessGalleryType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessGalleryAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', MediaPreviewType::class, [
                'class' => Media::class,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control textarea-control',
                    'rows' => 3,
                    'required' => true,
                ],
            ])
            ->add('position', HiddenType::class)
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
}
