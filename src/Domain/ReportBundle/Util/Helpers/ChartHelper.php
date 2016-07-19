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
            1 => '#8B0000', // Dark red
            2 => '#D2B48C', // Tan
            3 => '#008080', // Teal
            4 => '#7B68EE', // MediumSlateBlue
            5 => '#000080', // Navy
            6 => '#FF69B4', // HotPink
        ];
    }
}
