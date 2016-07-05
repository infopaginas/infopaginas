<?php

namespace Oxa\Sonata\AdminBundle\Model;

/**
 * Used for copping objects
 *
 * Interface DatetimePeriodInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface DatetimePeriodInterface
{
    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate($startDate);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return $this
     */
    public function setEndDate($endDate);

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @return boolean
     */
    public function isExpired();
}
