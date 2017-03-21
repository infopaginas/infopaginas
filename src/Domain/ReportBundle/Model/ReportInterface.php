<?php

namespace Domain\ReportBundle\Model;

/**
 * Class ReportInterface
 * @package Domain\ReportBundle\Model
 */
interface ReportInterface
{
    const FORMAT_PDF = 'pdf';
    const FORMAT_EXCEL = 'xls';

    const CODE_PDF_SUBSCRIPTION_REPORT              = 'pdf_subscription_report';
    const CODE_EXCEL_SUBSCRIPTION_REPORT            = 'excel_subscription_report';

    const CODE_PDF_CATEGORY_REPORT                  = 'pdf_category_report';
    const CODE_EXCEL_CATEGORY_REPORT                = 'excel_category_report';

    const CODE_PDF_BUSINESS_OVERVIEW_REPORT         = 'pdf_business_overview_report';
    const CODE_EXCEL_BUSINESS_OVERVIEW_REPORT       = 'excel_business_overview_report';

    public static function getExportFormats();
}
