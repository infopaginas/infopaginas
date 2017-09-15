<?php
namespace Domain\BusinessBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class MediaPreviewType
 * @package Domain\BusinessBundle\Form\Type
 */
class MediaPreviewType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return EntityHiddenType::class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media_preview';
    }
}
