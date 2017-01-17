<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 07.09.16
 * Time: 0:09
 */

namespace Domain\ReportBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\DfpBundle\Entity\DoubleClickLineItem;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\CoreBundle\Form\Type\EqualType;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AdUsageReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class AdUsageReportAdmin extends ReportAdmin
{
    const KEYWORDS_PER_PAGE_COUNT = [5, 10, 15, 20, 25];

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->remove('businessProfiles')
            ->remove('date')
            ->remove('periodOption')
            ->add('businessProfiles', 'doctrine_orm_choice', [
                'field_type' => 'choice',
                'field_options' => [
                    'mapped' => false,
                    'required'  => true,
                    'empty_value'  => null,
                    'choices'   => $this->getBusinessProfilesForFilter(),
                    'translation_domain' => 'SonataAdminBundle',
                    'attr' => [
                    ],
                ],
            ])
            ->add('date', 'doctrine_orm_datetime_range', AdminHelper::getDatagridDateTypeOptions())
        ;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return BusinessOverviewReport::getExportFormats();
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $dateFrom = \DateTime::createFromFormat('d-m-Y', $filterParam['date']['value']['start']);
        $dateTo   = \DateTime::createFromFormat('d-m-Y', $filterParam['date']['value']['end']);

        $dateRange = new DateRangeVO($dateFrom, $dateTo);

        if (!isset($filterParam['businessProfiles']['value'])) {
            $businessProfiles = $this->getBusinessProfilesForFilter();
            if (!empty($businessProfiles)) {
                reset($businessProfiles);
                $businessProfileId = key($businessProfiles);
            }
        } else {
            $businessProfileId = $filterParam['businessProfiles']['value'];
        }

        if (isset($businessProfileId)) {
            $item = $this->loadDataFromCache(
                $businessProfileId,
                $filterParam['date']['value']['start'],
                $filterParam['date']['value']['end']
            );

            if (!$item->isHit()) {
                $businessProfile = $this->getDoctrine()->getRepository(BusinessProfile::class)
                    ->find($businessProfileId);

                $lineItemIds = $this->getDoctrine()->getRepository(DoubleClickLineItem::class)
                    ->getLineItemIdsByBusinessProfile($businessProfile);

                $this->adUnits = $this->getDfpManager()->getStatsForMultipleLineItems($lineItemIds, $dateRange);

                $this->saveDataToCache($item, $this->adUnits);
            } else {
                $this->adUnits = $item->get();
            }
        } else {
            $this->adUnits = [];
        }
    }

    /**
     * Manage filter parameters
     *
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();
        $datePeriodParams = AdminHelper::getDataPeriodParameters();

        if (!isset($parameters['date'])) {
            $parameters = $this->datagridValues = array_merge(
                $parameters,
                [
                    'date' => [
                        'value' => $datePeriodParams[AdminHelper::DATE_RANGE_CODE_LAST_MONTH],
                    ]
                ]
            );
        } else {
            $parameters['date']['value'] = $this->getValidDateRange(
                $parameters['date'],
                $datePeriodParams[AdminHelper::DATE_RANGE_CODE_LAST_MONTH]
            );
        }

        return $parameters;
    }

    protected function getCacheService() : CacheItemPoolInterface
    {
        return $this->getConfigurationPool()->getContainer()->get('cache.provider.my_file_system');
    }

    protected function getDfpManager()
    {
        return $this->getConfigurationPool()->getContainer()->get('oxa_dfp.manager');
    }

    protected function getDoctrine()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine');
    }

    protected function getBusinessProfilesForFilter()
    {
        return $this->getDoctrine()->getRepository(BusinessProfile::class)
            ->getBusinessProfilesWithAllowedAdUnitsForFilter();
    }

    /**
     * @param string $businessProfileSlug
     * @param string $startDate
     * @param string $endDate
     * @return CacheItemInterface
     */
    protected function loadDataFromCache(
        string $businessProfileSlug,
        string $startDate,
        string $endDate
    ) : CacheItemInterface {
        $cacheKey = sha1($businessProfileSlug . $startDate . '-' . $endDate);
        return $this->getCacheService()->getItem($cacheKey);
    }

    /**
     * @param CacheItemInterface $item
     * @param $data
     */
    protected function saveDataToCache(CacheItemInterface $item, $data)
    {
        $item->set($data)->expiresAfter(3600);
        $this->getCacheService()->save($item);
    }
}