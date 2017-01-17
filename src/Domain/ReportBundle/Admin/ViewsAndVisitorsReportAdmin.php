<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\EqualType;

/**
 * Class BusinessOverviewReportAdmin
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
            ->remove('date')
            ->remove('periodOption')
            ->add('date', 'doctrine_orm_datetime_range', [
                'show_filter' => true,
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => [
                    'field_options' => [
                        'format' => AdminHelper::FILTER_DATE_RANGE_FORMAT,
                        'empty_value'  => false,
                    ],
                    'attr' => [
                        'class' => AdminHelper::FILTER_DATE_RANGE_CLASS
                    ],
                    'mapped' => false,
                    'required'  => true,
                ]
            ])
            ->add('periodOption', 'doctrine_orm_choice', AdminHelper::getDatagridPeriodOptionOptions())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->viewsAndVisitorsData = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.views_and_visitors')
            ->getViewsAndVisitorsData($filterParam);

        $this->colors = ChartHelper::getColors();

        $listMapper
            ->add('date', null, ['sortable' => false])
            ->add('views')
            ->add('visitors')
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
}
