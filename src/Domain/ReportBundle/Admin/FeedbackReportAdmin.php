<?php

namespace Domain\ReportBundle\Admin;

use Domain\PageBundle\Entity\Page;
use Domain\ReportBundle\Manager\FeedbackReportManager;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class FeedbackReportAdmin
 * @package Domain\ReportBundle\Admin
 */
class FeedbackReportAdmin extends ReportAdmin
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
            ->add('fullName', 'doctrine_orm_string', [
                'show_filter' => !empty($this->datagridValues['fullName']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('businessName', 'doctrine_orm_string', [
                'show_filter' => !empty($this->datagridValues['businessName']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('phone', 'doctrine_orm_string', [
                'show_filter' => !empty($this->datagridValues['phone']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('email', 'doctrine_orm_string', [
                'show_filter' => !empty($this->datagridValues['email']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('message', 'doctrine_orm_string', [
                'show_filter' => !empty($this->datagridValues['message']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('subject', 'doctrine_orm_choice', [
                'show_filter' => !empty($this->datagridValues['subject']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'choices' => Page::getContactSubjects(),
                ],
                'field_type' => 'choice'
            ])
            ->add('locale', 'doctrine_orm_choice', [
                'show_filter' => !empty($this->datagridValues['locale']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'choices' => LocaleHelper::getLocaleList(),
                ],
                'field_type' => 'choice'
            ])
            ->add('date', 'doctrine_orm_date_range', [
                'show_filter' => $this->checkDateFilter() ?: null,
                'field_type'  => 'sonata_type_date_range_picker',
                'field_options' => [
                    'field_options' => [
                        'format'        => AdminHelper::FILTER_DATE_RANGE_FORMAT,
                        'empty_value'   => false,
                    ],
                    'mapped'    => false,
                    'required'  => true,
                ],
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParam = $this->getFilterParameters();

        $this->feedbacks = $this->getFeedbackReportManager()->getFeedbackReportData($filterParam);
        $this->locales = LocaleHelper::getLocaleList();
        $this->subjects = Page::getContactSubjects();
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
        ;
    }

    /**
     * @return FeedbackReportManager
     */
    protected function getFeedbackReportManager() : FeedbackReportManager
    {
        return $this->getConfigurationPool()->getContainer()->get('domain_report.manager.feedback_report_manager');
    }

    /**
     * @return bool
     */
    protected function checkDateFilter()
    {
        if (!empty($this->datagridValues['date']['value']['start']) or
            !empty($this->datagridValues['date']['value']['end'])
        ) {
            return true;
        } else {
            return false;
        }
    }
}
