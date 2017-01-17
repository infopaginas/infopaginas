<?php

namespace Application\Sonata\ClassificationBundle\Document;

use Sonata\ClassificationBundle\Document\BaseTag as BaseTag;

class Tag extends BaseTag
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}