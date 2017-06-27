<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

use Oxa\Sonata\AdminBundle\Model\PostponeRemoveInterface;

/**
 * Should be implemented to entities that deleted by cron command
 *
 * Class PostponeRemoveTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait PostponeRemoveTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean", options={"default" : 0})
     */
    protected $isDeleted = false;

    /**
     * @param boolean $isDeleted
     *
     * @return PostponeRemoveInterface
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
}
