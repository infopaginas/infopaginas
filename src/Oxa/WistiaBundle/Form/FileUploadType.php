<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 16:19
 */

namespace Oxa\WistiaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class)
            ->add('submit', SubmitType::class)
        ;
    }
}
