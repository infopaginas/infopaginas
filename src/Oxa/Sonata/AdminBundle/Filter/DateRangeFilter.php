<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Sonata\Form\Type\DateRangeType;

class DateRangeFilter extends OxaAbstractDateFilter
{
    /**
     * This is a range filter
     * @var boolean
     */
    protected $range = true;

    /**
     * This filter has time
     * @var boolean
     */
    protected $time = false;

    /**
     * {@inheritdoc}
     */
    public function getFieldType()
    {
        return $this->getOption('field_type', DateRangeType::class);
    }
}
