<?php

namespace Domain\ReportBundle\Admin;

use Domain\PageBundle\Entity\Page;
use Domain\ReportBundle\Entity\FeedbackReport;
use Domain\ReportBundle\Manager\FeedbackReportManager;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\Sonata\AdminBundle\Filter\CaseInsensitiveStringFilter;
use Oxa\Sonata\AdminBundle\Filter\DateRangeFilter;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\Form\Type\DateRangePickerType;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
     * @return array
     */
    public function getExportFormats()
    {
        return FeedbackReport::getExportFormats();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fullName', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['fullName']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('businessName', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['businessName']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('phone', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['phone']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('email', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['email']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('message', CaseInsensitiveStringFilter::class, [
                'show_filter' => !empty($this->datagridValues['message']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                ],
            ])
            ->add('subject', ChoiceFilter::class, [
                'show_filter' => !empty($this->datagridValues['subject']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'choices' => array_flip(Page::getAllSubjects()),
                ],
                'field_type' => ChoiceType::class
            ])
            ->add('locale', ChoiceFilter::class, [
                'show_filter' => !empty($this->datagridValues['locale']['value']) ?: null,
                'field_options' => [
                    'mapped'    => false,
                    'choices' => array_flip(LocaleHelper::getLocaleList()),
                ],
                'field_type' => ChoiceType::class
            ])
            ->add('date', DateRangeFilter::class, [
                'show_filter' => $this->checkDateFilter() ?: null,
                'field_type' => DateRangePickerType::class,
                'field_options' => [
                    'field_options' => [
                        'format'        => AdminHelper::FILTER_DATE_RANGE_FORMAT,
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
        $this->locales   = LocaleHelper::getLocaleList();
        $this->subjects  = Page::getAllSubjects();
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
        if (
            !empty($this->datagridValues['date']['value']['start']) ||
            !empty($this->datagridValues['date']['value']['end'])
        ) {
            return true;
        }

        return false;
    }
}
