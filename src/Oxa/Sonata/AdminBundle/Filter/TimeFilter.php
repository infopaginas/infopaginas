<?php

namespace Oxa\Sonata\AdminBundle\Filter;

class TimeFilter extends OxaAbstractDateFilter
{
    /**
     * This filter has no range
     *
     * @var boolean
     */
    protected $range = false;

    /**
     * This filter does not allow filtering by time
     *
     * @var boolean
     */
    protected $time = true;

    /**
     * {@inheritdoc}
     */
    public function getFieldType()
    {
        return $this->getOption('field_type', 'time');
    }
}
