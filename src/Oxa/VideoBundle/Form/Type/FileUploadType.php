<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 16:19
 */

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

    /**
     * @return string
     */
    public function getName()
    {
        return 'oxa_video_bundle_file_upload_form_type';
    }
}
