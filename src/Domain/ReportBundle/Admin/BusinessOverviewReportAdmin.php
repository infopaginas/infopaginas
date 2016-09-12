<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\CoreBundle\Form\Type\EqualType;

/**
 * Class BusinessOverviewReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class BusinessOverviewReportAdmin extends ReportAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

        $datagridMapper
            ->remove('date')
            ->remove('datePeriod')
            ->remove('businessOverviewReportBusinessProfiles.businessProfile')
            ->remove('periodOption')
            ->add('businessOverviewReportBusinessProfiles.businessProfile', null, [
                'label' => $this->trans('filter.label_business_profile', [], $this->getTranslationDomain()),
            ], null, [
                'mapped' => false,
                'empty_value' => null,
            ])
            ->add('datePeriod', 'doctrine_orm_choice', AdminHelper::getDatagridDatePeriodOptions())
            ->add('date', 'doctrine_orm_datetime_range', AdminHelper::getDatagridDateTypeOptions())
            ->add('periodOption', 'doctrine_orm_choice', AdminHelper::getDatagridPeriodOptionOptions())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getDatagrid()->getValues();

        $this->businessOverviewData = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.business_overview_report_manager')
            ->getBusinessOverviewDataByFilterParams($filterParam);

        $this->colors = ChartHelper::getColors();

        $listMapper
            ->add('date', null, ['sortable' => false])
            ->add('impressions')
            ->add('views')
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
     * Manage filter parameters
     *
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();

        $datePeriodParams = AdminHelper::getDataPeriodParameters();
        $allowedDatePeriodCodes = array_keys($datePeriodParams);

        if (isset($parameters['datePeriod'])) {
            // if datePeriod is set
            // apply it's data range in force way
            if (isset($parameters['datePeriod']['value']) && $datePeriodCode = $parameters['datePeriod']['value']) {
                if ($datePeriodCode == AdminHelper::DATE_RANGE_CODE_CUSTOM) {
                    return $parameters;
                }

                if (!in_array($datePeriodCode, $allowedDatePeriodCodes)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            '"%s" is not allowed, must be one of: %s',
                            $datePeriodCode,
                            implode(', ', $allowedDatePeriodCodes)
                        )
                    );
                }

                $parameters = $this->datagridValues = array_merge(
                    $parameters,
                    [
                        'date' => [
                            'type' => EqualType::TYPE_IS_EQUAL,
                            'value' => $datePeriodParams[$datePeriodCode],
                        ]
                    ]
                );
            } else {
                unset($parameters['datePeriod']);
            }
        }
        return $parameters;
    }
}
