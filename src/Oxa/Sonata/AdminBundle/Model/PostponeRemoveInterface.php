<?php

namespace Oxa\Sonata\AdminBundle\Model;

/**
 * Should be implemented to entities that deleted by cron command
 *
 * Interface PostponeRemoveInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface PostponeRemoveInterface
{
    /**
     * @param  bool $isDeleted
     *
     * @return PostponeRemoveInterface
     */
    public function setIsDeleted($isDeleted);

    /**
     * @return bool
     */
    public function getIsDeleted();
}
