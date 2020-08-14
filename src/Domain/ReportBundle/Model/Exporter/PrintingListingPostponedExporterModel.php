<?php

namespace Domain\ReportBundle\Model\Exporter;

/**
 * Class PrintingListingPostponedExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class PrintingListingPostponedExporterModel extends CsvPostponedExporterModel
{
    protected const FORMAT = 'tsv';

    protected function writeToFile($row)
    {
        fputcsv($this->streamResource, $row, "\t");
    }
}
