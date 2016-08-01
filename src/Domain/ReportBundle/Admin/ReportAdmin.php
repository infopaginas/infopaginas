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

class ReportAdmin extends OxaAdmin
{
    protected $translationDomain = 'AdminReportBundle';

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 25,
        'datePeriod' => [
            'value' => AdminHelper::DATE_RANGE_CODE_LAST_WEEK
        ]
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
}
