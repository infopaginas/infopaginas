<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 11.08.16
 * Time: 22:02
 */

namespace Domain\BusinessBundle\Form\Type;

use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Util\DatesUtil;
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
                    'class' => 'form-control select-control',
                ],
                'choices' => DatesUtil::getReportDataRanges(),
                'data' => DatesUtil::RANGE_DEFAULT,
            ])
            ->add('start',  DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker form-control'],
                'data' => new \DateTime("monday this week")
            ))
            ->add('end',  DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker form-control'],
                'data' => new \DateTime("sunday this week")
            ))
            ->add('limit', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control select-control',
                ],
                'choices' => KeywordsReportManager::KEYWORDS_PER_PAGE_COUNT,
                'data' => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
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
