<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessReportFilterType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessReportFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateRange', ChoiceType::class, [
                'attr' => [
                    'class' => 'select--medium',
                ],
                'choices' => DatesUtil::getReportDataRanges(),
                'data'    => DatesUtil::RANGE_LAST_MONTH,
            ])
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'html5'  => false,
                'attr'   => [
                    'class' => 'js-datepicker form-control',
                ],
                'data' => new \DateTime('monday this week'),
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'html5'  => false,
                'attr'   => [
                    'class' => 'js-datepicker form-control'
                ],
                'data' => new \DateTime('sunday this week'),
            ])
            ->add('limit', ChoiceType::class, [
                'label' => 'Keywords Count',
                'label_attr' => [
                    'class' => 'title-label',
                ],
                'attr' => [
                    'class' => 'select--medium',
                ],
                'choices' => KeywordsReportManager::KEYWORDS_PER_PAGE_COUNT,
                'data'    => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
            ])
            ->add('actionType', ChoiceType::class, [
                'label' => 'business_profile.interaction_chart.action_type',
                'label_attr' => [
                    'class' => 'title-label',
                ],
                'attr' => [
                    'class' => 'select--medium',
                ],
                'choices' => BusinessOverviewModel::getChartEventTypesWithTranslation(),
                'data'    => BusinessOverviewModel::DEFAULT_CHART_TYPE,
            ])
            ->add('groupPeriod', ChoiceType::class, [
                'label' => 'business_profile.interaction_chart.group_period',
                'label_attr' => [
                    'class' => 'title-label',
                ],
                'attr' => [
                    'class' => 'select--medium',
                ],
                'choices' => AdminHelper::getPeriodOptionValues(),
                'data'    => AdminHelper::PERIOD_OPTION_CODE_PER_MONTH,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_report_filter_type';
    }
}
