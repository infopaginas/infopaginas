<?php

namespace Domain\ReportBundle\Admin;

use Doctrine\ORM\Query;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;
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
        $datagridMapper
            ->remove('date')
            ->remove('datePeriod')
            ->add('datePeriod', 'doctrine_orm_choice', AdminHelper::getDatagridDatePeriodOptions())
            ->add('date', 'doctrine_orm_datetime_range', AdminHelper::getDatagridDateTypeOptions())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $subscriptionReports = $this->getDatagrid()->getResults();

        $listMapper
            ->add('date', null, ['sortable' => false])
        ;
        $subscriptionReportManager = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_report.manager.subscription_report_manager');

        $subscriptionPlans = $subscriptionReportManager->getSubscriptionPlans();

        $parameters = parent::getFilterParameters();

        if ($parameters['datePeriod']['value'] == 'custom') {
            $dateRange = DatesUtil::getDateAsDateRangeVOFromRequestData($parameters['date']['value'], 'd-m-Y');
        } else {
            $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType($parameters['datePeriod']['value']);
        }

        $dates = DatesUtil::dateRange($dateRange);

        //dates'll has one non-required day
        unset($dates[count($dates) -1]);

        $this->subscriptionData = $subscriptionReportManager->getSubscriptionsQuantities($subscriptionReports, $dates, $subscriptionPlans);

        $this->colors = ChartHelper::getColors();

        $locale = $this->getConfigurationPool()
            ->getContainer()
            ->getParameter('locale');

        foreach ($subscriptionPlans as $subscriptionPlan) {
            $listMapper
                ->add($subscriptionPlan->getName(), null, [
                    'label' => $subscriptionPlan->getTranslation('name', $locale)
                ])
            ;
        }

        $listMapper
            ->add('total')
        ;
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
