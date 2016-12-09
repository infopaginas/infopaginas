<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 16:19
 */

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
                    'class' => 'form-control',
                ],
                'label' => 'Title',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control media-description',
                ],
                'label' => 'Description',
            ])
            ->add('videoId', HiddenType::class, [])
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
