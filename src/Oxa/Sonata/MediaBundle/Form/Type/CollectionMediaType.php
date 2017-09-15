<?php

namespace Oxa\Sonata\MediaBundle\Form\Type;

use Sonata\AdminBundle\Form\Type\CollectionType;

class CollectionMediaType extends CollectionType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'sonata_type_native_collection_media';
    }
}
