<?php

namespace Domain\ReportBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Domain\ReportBundle\Util\DatesUtil;

class ReportAdmin extends OxaAdmin
{
    protected $translationDomain = 'AdminReportBundle';

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'     => 1,
        '_per_page' => 25,
    );

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection
            ->add('export')
            ->remove('edit')
            ->remove('create')
            ->remove('delete')
            ->remove('restore')
            ->remove('delete_physical')
        ;
    }

    /**
     * get valid date range field values for custom process field
     *
     * @param array $dateRange
     * @param array $defaultValues
     *
     * @return array
     */
    protected function getValidDateRange($dateRange, $defaultValues)
    {
        $result = [];

        $result['start'] = DatesUtil::isValidDateString($dateRange['value']['start']) ?
            $dateRange['value']['start'] : $defaultValues['start'];
        $result['end'] = DatesUtil::isValidDateString($dateRange['value']['end']) ?
            $dateRange['value']['end'] : $defaultValues['end'];

        return $result;
    }
}
