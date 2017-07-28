<?php

namespace Domain\ReportBundle\Admin;

use Doctrine\ORM\Query;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\EqualType;

/**
 * Class SubscriptionReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class SubscriptionReportAdmin extends ReportAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('date', 'doctrine_orm_datetime_range', AdminHelper::getReportDateTypeOptions());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $parameters = $this->getFilterParameters();

        $this->subscriptionData = $this->getSubscriptionReportManager()->getSubscriptionsReportData($parameters);

        $this->colors = ChartHelper::getColors();
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return SubscriptionReport::getExportFormats();
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
     * @return SubscriptionReportManager
     */
    protected function getSubscriptionReportManager() : SubscriptionReportManager
    {
        return $this->getConfigurationPool()->getContainer()->get('domain_report.manager.subscription_report_manager');
    }
}
