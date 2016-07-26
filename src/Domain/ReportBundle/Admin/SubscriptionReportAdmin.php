<?php

namespace Domain\ReportBundle\Admin;

use Doctrine\ORM\Query;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Util\Helpers\ChartHelper;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class SubscriptionReportAdmin extends OxaAdmin
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
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->remove('date')
            ->remove('datePeriod')
            ->add('datePeriod', 'doctrine_orm_choice', AdminHelper::getDatagridDatePeriodOptions())
            ->add('date', 'doctrine_orm_datetime_range', $this->defaultDatagridDateTypeOptions)
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

        $this->subscriptionData = $subscriptionReportManager->getSubscriptionsQuantities($subscriptionReports);
        $this->colors = ChartHelper::getColors();

        $locale = $this->getRequest()->getLocale();

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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('subscriptionReportSubscriptions')
            ->add('date', 'sonata_type_date_picker', ['format' => self::FORM_DATE_FORMAT])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('subscriptionReportSubscriptions')
            ->add('date')
        ;
    }

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
                            'type' => 1,
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
