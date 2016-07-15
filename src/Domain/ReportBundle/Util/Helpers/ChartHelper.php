<?php

namespace Domain\ReportBundle\Util\Helpers;

/**
 * Class ChartHelper
 * @package Oxa\Sonata\AdminBundle\Util\Helpers
 */
class ChartHelper
{
    /**
     * @return array
     */
    public static function getColors()
    {
        return [
            1 => 'DarkRed',
            2 => 'Tan',
            3 => 'Teal',
            4 => 'MediumSlateBlue',
            5 => 'Navy',
            6 => 'HotPink',
        ];
    }
}
