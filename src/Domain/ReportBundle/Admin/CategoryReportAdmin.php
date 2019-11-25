<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Class CategoryReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class CategoryReportAdmin extends ReportAdmin
{
    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * Basic admin configuration
     */
    public function configure()
    {
        parent::configure();

        $this->setPerPageOptions(
            [
                5,
                10,
                15,
                20,
                25,
                50,
                100,
                500,
            ]
        );
    }

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'     => 1,
        '_per_page' => 15,
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', 'doctrine_orm_string', [
                'show_filter' => !empty($this->datagridValues['name']['value']) ?: null,
                'field_options' => [
                    'mapped' => false,
                ],
            ])
            ->add('searchTextEs', 'doctrine_orm_string', [
                'label' => 'Name Esp',
                'show_filter' => !empty($this->datagridValues['searchTextEs']['value']) ?: null,
                'field_options' => [
                    'mapped' => false,
                ],
            ])
            ->add('date', 'doctrine_orm_datetime_range', AdminHelper::getReportDateTypeOptions())
            ->add('locality', 'doctrine_orm_model', [
                'show_filter' => !empty($this->datagridValues['locality']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'property'  => 'name',
                    'class' => 'Domain\BusinessBundle\Entity\Locality',
                ],
            ])
            ->add('type', 'doctrine_orm_choice', [
                'show_filter' => !empty($this->datagridValues['type']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'choices' => CategoryOverviewReportManager::getCategoryPageType(),
                    'choice_translation_domain' => 'AdminReportBundle',
                ],
                'field_type' => 'choice'
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->categoryData = $this->getCategoryOverviewReportManager()->getCategoryReportData($filterParam);

        $this->colors = ChartHelper::getColors();
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return CategoryReport::getExportFormats();
    }

    /**
     * @return mixed
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

    /**
     * @return CategoryOverviewReportManager
     */
    protected function getCategoryOverviewReportManager() : CategoryOverviewReportManager
    {
        return $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.category_overview_report_manager');
    }
}
