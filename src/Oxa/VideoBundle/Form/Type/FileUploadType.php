<?php

namespace Oxa\VideoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FileUploadType
 * @package Oxa\VideoBundle\Form\Type
 */
class FileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class)
        ;
    }
}
