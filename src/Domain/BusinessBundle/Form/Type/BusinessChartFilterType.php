<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\ReportBundle\Model\BusinessOverviewModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class BusinessChartFilterType extends BusinessReportFilterType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('actionType', ChoiceType::class, [
            'label' => 'business_profile.interaction_chart.action_type',
            'label_attr' => [
                'class' => 'title-label',
            ],
            'choices' => array_flip(BusinessOverviewModel::getAllChartEventTypesWithTranslation()),
            'data'    => BusinessOverviewModel::DEFAULT_CHART_TYPE,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'domain_business_bundle_business_chart_filter_type';
    }
}
