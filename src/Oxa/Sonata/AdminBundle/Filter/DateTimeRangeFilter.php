<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Sonata\CoreBundle\Form\Type\DateTimeRangeType;

class DateTimeRangeFilter extends OxaAbstractDateFilter
{
    /**
     * This Filter allows filtering by time
     *
     * @var boolean
     */
    protected $time = true;

    /**
     * This is a range filter
     *
     * @var boolean
     */
    protected $range = true;

    /**
     * {@inheritdoc}
     */
    public function getFieldType()
    {
        return $this->getOption('field_type', DateTimeRangeType::class);
    }
}
