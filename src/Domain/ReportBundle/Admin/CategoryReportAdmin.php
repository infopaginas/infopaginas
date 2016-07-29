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
        '_page'       => 0,
        '_per_page'   => 5,
        'datePeriod' => [
            'value' => AdminHelper::DATE_RANGE_CODE_LAST_WEEK
        ]
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->remove('datePeriod')
            ->remove('categoryReportCategories.date')
            ->add('datePeriod', 'doctrine_orm_choice', AdminHelper::getDatagridDatePeriodOptions())
            ->add('categoryReportCategories.date', 'doctrine_orm_datetime_range', $this->defaultDatagridDateTypeOptions)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getDatagrid()->getValues();
        $this->categoryData = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.category_report_manager')
            ->getCategoryVisitorsQuantitiesByFilterParams($filterParam);

        $this->colors = ChartHelper::getColors();

        $listMapper
            ->add('category')
            ->add('total')
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
        $allowedDatePeriodCodes = array_keys($datePeriodParams);

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
                        'categoryReportCategories__date' => [
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
