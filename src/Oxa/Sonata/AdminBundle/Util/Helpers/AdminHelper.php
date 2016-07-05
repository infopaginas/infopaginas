<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/5/16
 * Time: 11:44 AM
 */

namespace Oxa\Sonata\AdminBundle\Util\Helpers;

use Domain\BusinessBundle\Util\Traits\StatusTrait;

/**
 * Class AdminHelper
 * @package Oxa\Sonata\AdminBundle\Util\Helpers
 */
class AdminHelper
{
    /**
     * @return array
     */
    public static function getDatagridStatusOptions()
    {
        return [
            'field_type' => 'choice',
            'field_options' => [
                'required'  => false,
                'choices'   => StatusTrait::getStatuses()
            ]
        ];
    }
}
