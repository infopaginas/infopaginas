<?php

namespace Oxa\VideoBundle\Form\Type;

use Domain\BusinessBundle\Form\Type\EntityHiddenType;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

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
                    'readonly'  => true,
                ],
                'label' => 'Name',
            ])
            ->add('title', TextType::class, [
                'attr' => [
                    'class'     => 'form-control',
                ],
                'label' => 'Title',
                'constraints' => array(
                    new Length(
                        [
                            'max' => VideoMedia::VIDEO_TITLE_MAX_LENGTH,
                        ]
                    ),
                ),
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class'     => 'form-control',
                ],
                'label' => 'Description',
                'constraints' => array(
                    new Length(
                        [
                            'max' => VideoMedia::VIDEO_TITLE_MAX_DESCRIPTION,
                        ]
                    ),
                ),
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
}
