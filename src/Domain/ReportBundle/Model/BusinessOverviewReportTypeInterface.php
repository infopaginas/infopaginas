<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/16/16
 * Time: 8:13 PM
 */

namespace Domain\ReportBundle\Model;

/**
 * Class BusinessOverviewReportTypeInterface
 * @package Domain\ReportBundle\Model
 */
interface BusinessOverviewReportTypeInterface
{
    const TYPE_CODE_IMPRESSION   = 1;
    const TYPE_CODE_VIEW         = 2;

    public static function getTypes();

    public function getTypeValue();
}
