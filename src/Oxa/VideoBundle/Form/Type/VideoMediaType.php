<?php

namespace Oxa\VideoBundle\Form\Type;

use Domain\BusinessBundle\Form\Type\EntityHiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VideoMediaType
 * @package Oxa\VideoBundle\Form\Type
 */
class VideoMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class'     => 'form-control',
                    'disabled'  => true,
                ],
                'label' => 'Name',
            ])
            ->add('id', HiddenType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Oxa\VideoBundle\Entity\VideoMedia',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oxa_video_bundle_video_media_form_type';
    }
}
