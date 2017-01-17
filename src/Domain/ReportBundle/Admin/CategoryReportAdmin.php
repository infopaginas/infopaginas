<?php

namespace Domain\ReportBundle\Admin;

use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\EqualType;

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
    protected $maxPerPage = 5;

    /**
     * category report date field
     *
     * @var string
     */
    protected $reportDateField = 'categoryReportCategories__date';

    /**
     * Basic admin configuration
     */
    public function configure()
    {
        parent::configure();

        $this->setPerPageOptions([
            5, 10, 15, 20, 500
        ]);

        // custom delete page template
        $this->setTemplate('delete', 'OxaSonataAdminBundle:CRUD:delete.html.twig');
    }

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'     => 1,
        '_per_page' => 5,
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->remove('categoryReportCategories.date')
            ->add(
                'categoryReportCategories.date',
                'doctrine_orm_datetime_range',
                array_merge(
                    AdminHelper::getDatagridDateTypeOptions(),
                    [
                        'label' => $this->trans('filter.label_date', [], $this->translationDomain)
                    ]
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->categoryData = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.category_report_manager')
            ->getCategoryVisitorsQuantitiesByFilterParams($filterParam);

        $this->colors = ChartHelper::getColors();

        $listMapper
            ->add('category')
            ->add('total', null, ['label' => 'Number of visited profiles'])
        ;
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

        if (isset($parameters['_per_page']) && $parameters['_per_page'] == AdminHelper::PER_PAGE_ALL) {
            $perPageAll = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine.orm.entity_manager')
                ->getRepository('DomainBusinessBundle:Category')
                ->getAllCategoriesCount();

            $parameters = $this->datagridValues = array_merge(
                $parameters,
                [
                    '_per_page' => $perPageAll
                ]
            );
        }

        if (!isset($parameters[$this->reportDateField])) {
            $parameters = $this->datagridValues = array_merge(
                $parameters,
                [
                    $this->reportDateField => [
                        'value' => $datePeriodParams[AdminHelper::DATE_RANGE_CODE_LAST_MONTH],
                    ]
                ]
            );
        } else {
            $parameters[$this->reportDateField]['value'] = $this->getValidDateRange(
                $parameters[$this->reportDateField],
                $datePeriodParams[AdminHelper::DATE_RANGE_CODE_LAST_MONTH]
            );
        }

        return $parameters;
    }
}
