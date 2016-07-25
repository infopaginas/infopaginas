<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 17:34
 */

namespace Oxa\WistiaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UrlUploadType
 * @package Oxa\WistiaBundle\Form\Type
 */
class UrlUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class)
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oxa_wistia_bundle_url_upload_form_type';
    }
}
