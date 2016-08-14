<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

use Oxa\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DatetimePeriodTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait DatetimePeriodTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="datetime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime")
     */
    protected $endDate;

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        if ($this->getEndDate() instanceof \DateTime) {
            $datetime = new \DateTime('now');
            $diff = $datetime->diff($this->getEndDate());

            return boolval($diff->invert);
        }

        return true;
    }
}
