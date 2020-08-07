<?php

namespace Domain\ReportBundle\Service\Export;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\ReportBundle\Model\Exporter\PrintingListingPostponedExporterModel;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;

/**
 * Class BusinessProfilePrintingListingExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessProfilePrintingListingExporter extends PrintingListingPostponedExporterModel
{
    protected const REGULAR_LISTING = 'RL';
    protected const EXTRA_LISTING   = 'EL';

    /**
     * @var EntityManager $em
     */
    protected $em;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array  $parameters
     */
    protected function setData($parameters = [])
    {
        $dataIterator = $this->em->getRepository(BusinessProfile::class)->getBusinessProfilePrintingListingIterator();

        $this->initProperties();

        /** @var BusinessProfile $bp */
        foreach ($dataIterator as $row) {
            $bp = $row[0];

            if ($this->isNewPage) {
                $path = $this->generateTempFilePath($parameters['exportPath'], $this->page);

                $this->createStreamResource($path);
                $this->generateHeaderTable();

                $this->isNewPage = false;
            }

            foreach ($bp->getCategories() as $category) {
                $isRegularListing = true;
                foreach ($bp->getPhones() as $phone) {
                    if ($phone->getType() != BusinessProfilePhone::PHONE_TYPE_FAX) {
                        $data = [
                            '',
                            $phone->getPhone(),
                            (int) !$isRegularListing,
                            $isRegularListing ? $bp->getName() : '',
                            $isRegularListing ? $bp->getStreetAddress() : '',
                            $bp->getCatalogLocality()->getTranslation('name', LocaleHelper::LOCALE_ES),
                            '',
                            '',
                            'B',
                            '',
                            $isRegularListing ? self::REGULAR_LISTING : self::EXTRA_LISTING,
                            $category->getTranslation('name', LocaleHelper::LOCALE_ES),
                            $bp->getExportAreas(),
                        ];

                        $this->generateMainTable($data);

                        $isRegularListing = false;
                    }
                }
            }

            $this->em->clear();
        }

        unset($dataIterator);
    }

    /**
     * @param array $data
     */
    protected function generateMainTable($data)
    {
        if ($data) {
            $this->writeToFile($data);
        }
    }

    /**
     * @param array $headers
     */
    protected function generateHeaderTable()
    {
        $headers = [
            '***8',
            '****',
            '787',
            '*****',
            'Generic Listings Yellow',
            'PR',
            '',
            '',
            '',
            '',
            '',
            '',
            'Areas',
        ];

        $this->generateMainTable($headers);
    }
}
