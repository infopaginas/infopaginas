<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Manager\ViewsAndVisitorsReportManager;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;

/**
 * Class ViewsAndVisitorsReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class ViewsAndVisitorsReportAdmin extends ReportAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('date', DateTimeRangeFilter::class, AdminHelper::getReportDateTypeOptions())
            ->add('periodOption', ChoiceFilter::class, AdminHelper::getDatagridPeriodOptionOptions())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->viewsAndVisitorsData = $this->getViewsAndVisitorsReportManager()->getViewsAndVisitorsData($filterParam);

        $this->colors = ChartHelper::getColors();
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return BusinessOverviewModel::getExportFormats();
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

        // periodOption is set as 'daily' by default
        if (!isset($parameters['periodOption']) || !isset($parameters['periodOption']['value'])) {
            $parameters = $this->datagridValues = array_merge(
                $parameters,
                [
                    'periodOption' => [
                        'value' => AdminHelper::PERIOD_OPTION_CODE_DAILY,
                    ]
                ]
            );
        }

        return $parameters;
    }

    /**
     * @return ViewsAndVisitorsReportManager
     */
    protected function getViewsAndVisitorsReportManager() : ViewsAndVisitorsReportManager
    {
        return $this->getConfigurationPool()->getContainer()->get('domain_report.manager.views_and_visitors');
    }
}
