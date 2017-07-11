<?php

namespace Domain\ReportBundle\Model\Exporter;

/**
 * Class ExcelPostponedExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class ExcelPostponedExporterModel extends ExcelExporterModel
{
    protected $currentRow;
    protected $currentCol;

    protected $counter   = 0;
    protected $page      = 1;
    protected $isNewPage = true;
    protected $files     = [];

    protected $reportTitle = 'export.title.default';

    /**
     * @param string $title
     * @param array  $parameters
     */
    abstract protected function setData($title, $parameters = []);

    /**
     * @param array $parameters
     * @return Array
     * @throws \PHPExcel_Exception
     */
    public function getResponse($parameters = [])
    {
        $title = $this->translator->trans($this->reportTitle, [], 'AdminReportBundle');
        $title = $this->getSafeTitle($title);

        $this->setData($title, $parameters);

        return $this->files;
    }

    /**
     * @param string $title
     */
    protected function createPHPExcelObject($title)
    {
        $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $this->phpExcelObject->getProperties()->setTitle($title);
        $this->phpExcelObject->getActiveSheet()->setTitle($title);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function saveResponse($path)
    {
        try {
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $this->phpExcelObject->setActiveSheetIndex(0);
            $this->phpExcel->createWriter($this->phpExcelObject)->save($path);

            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        return $status;
    }

    /**
     * @param string $path
     * @param int    $page
     *
     * @return string
     */
    protected function generateTempFilePath($path, $page = 0)
    {
        return $path . uniqid('', true) . '_' . $page . '.' . self::FORMAT;
    }

    /**
     * @param string $path
     */
    protected function saveDataToFile($path)
    {
        $status = $this->saveResponse($path);

        if ($status) {
            $this->files[] = $path;
        }

        $this->counter = 0;
        $this->page++;
    }

    protected function initProperties()
    {
        $this->files     = [];
        $this->counter   = 0;
        $this->page      = 1;
        $this->isNewPage = true;
    }
}
