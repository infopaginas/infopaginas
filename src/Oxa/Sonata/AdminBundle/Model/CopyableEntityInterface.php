<?php

namespace Oxa\Sonata\AdminBundle\Model;

/**
 * Used for copping objects
 *
 * Interface CopyableEntityInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface CopyableEntityInterface
{
    /**
     * Choose a property, which will be used to make a copied object different by adding prefix
     * @return mixed
     */
    public function getMarkCopyPropertyName();
}
