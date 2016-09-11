<?php

namespace Oxa\Sonata\AdminBundle\Filter;

class DateTimeFilter extends OxaAbstractDateFilter
{
    /**
     * This filter has time
     *
     * @var boolean
     */
    protected $time = true;

    /**
     * This is not a rangle filter
     *
     * @var boolean
     */
    protected $range = false;

    /**
     * {@inheritdoc}
     */
    public function getFieldType()
    {
        return $this->getOption('field_type', 'datetime');
    }
}
