<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/16/16
 * Time: 8:13 PM
 */

namespace Domain\ReportBundle\Model;

/**
 * Class ReportInterface
 * @package Domain\ReportBundle\Model
 */
interface ReportInterface
{
    const FORMAT_PDF = 'pdf';
    const FORMAT_EXCEL = 'xls';

    const CODE_PDF_SUBSCRIPTION_REPORT      = 'pdf_subscription_report';
    const CODE_EXCEL_SUBSCRIPTION_REPORT    = 'excel_subscription_report';

    public static function getExportFormats();
}
