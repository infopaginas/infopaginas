<?php

namespace Domain\ReportBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\ReportBundle\Entity\ExportReport;
use Domain\ReportBundle\Model\ReportInterface;
use Domain\ReportBundle\Service\Export\Exporter;
use Domain\SiteBundle\Mailer\Mailer;
use Gaufrette\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostponeExportReportManager
{
    const EXPORT_REPORT_LIMIT = 5;

    /** @var  ContainerInterface $container */
    protected $container;

    /** @var  EntityManager $em */
    protected $em;

    /** @var  Exporter $exporter */
    protected $exporter;

    /** @var  Filesystem $filesystem */
    protected $filesystem;

    /** @var  Mailer $mailer */
    protected $mailer;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
        $this->em           = $this->container->get('doctrine.orm.entity_manager');
        $this->exporter     = $this->container->get('domain_report.exporter');
        $this->filesystem   = $this->container->get('report_storage_filesystem');
        $this->mailer       = $this->container->get('domain_site.mailer');
    }

    public function postponeExportReport()
    {
        $exports = $this->em->getRepository(ExportReport::class)
            ->getExportReportByStatusIterator(ExportReport::STATUS_PENDING);

        $exportPath = $this->getExportTempPath();

        if (!file_exists($exportPath)) {
            mkdir($exportPath);
        }

        $counter = 0;

        foreach ($exports as $row) {
            /** @var ExportReport $export */
            $export = current($row);
            $format = $export->getFormat();
            $links  = [];
            $status = ExportReport::STATUS_ERROR;

            $filePaths = $this->exporter->getResponse(
                $export->getClass(),
                $format,
                $export->getParams(),
                $exportPath
            );

            $this->em->clear();

            if ($filePaths) {
                foreach ($filePaths as $filePath) {
                    $fileData = $this->getFileData($format, $filePath);

                    if ($fileData) {
                        $result = $this->uploadLocalFileData($fileData);

                        if ($result['status']) {
                            $links[] = $result['link'];
                        }

                        unlink($filePath);
                    }
                }

                $status = ExportReport::STATUS_READY;
            }

            $this->mailer->sendReportExportProcessedEmailMessage($export);

            $this->em->getRepository(ExportReport::class)->setExportReportData(
                $export->getId(),
                $status,
                json_encode($links)
            );
            $this->em->clear();

            $counter++;

            if ($counter >= self::EXPORT_REPORT_LIMIT) {
                break;
            }
        }
    }

    /**
     * @param string $format
     * @param string $filePath
     *
     * @return array
     */
    protected function getFileData($format, $filePath)
    {
        switch ($format) {
            case ReportInterface::FORMAT_EXCEL:
                $type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case ReportInterface::FORMAT_PDF:
                $type = 'application/pdf';
                break;
            case ReportInterface::FORMAT_CSV:
                $type = 'text/csv';
                break;
            default:
                $type = '';
                break;
        }

        if ($type) {
            $fileData = [
                'type' => $type,
                'ext'  => $format,
                'path' => $filePath,
            ];
        } else {
            $fileData = [];
        }

        return $fileData;
    }

    /**
     * @param array
     *
     * @return array
     */
    protected function uploadLocalFileData($data)
    {
        $adapter = $this->filesystem->getAdapter();

        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.%s', uniqid('', true), $data['ext']);
        } while ($adapter->exists($path . $filename));

        $adapter->setMetadata($path . $filename, [
            'contentType'   => $data['type'],
            'ACL'           => 'public-read',
        ]);

        $uploadedSize = $adapter->write($path . $filename, file_get_contents($data['path']));

        return [
            'status' => (bool) $uploadedSize,
            'link'   => $this->getPublicUrl($path, $filename),
        ];
    }

    /**
     * @param string $path
     * @param string $filename
     *
     * @return string
     */
    public function getPublicUrl($path, $filename)
    {
        $url = sprintf(
            '%s/%s%s',
            $this->getCdnPath(),
            $path,
            $filename
        );

        return $url;
    }

    /**
     * @return string
     */
    protected function getExportTempPath()
    {
        return $this->container->get('kernel')->getRootDir() . $this->container->getParameter('export_report_path');
    }

    /**
     * @return string
     */
    protected function getCdnPath()
    {
        $path = sprintf(
            '%s/%s',
            $this->container->getParameter('amazon_aws_base_host'),
            $this->container->getParameter('amazon_aws_report_directory')
        );

        return $path;
    }
}
