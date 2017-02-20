<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Model\DayOfWeekModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BusinessProfileWorkingHourType
 * @package Domain\BusinessBundle\Form\Type
 */
class BusinessProfileWorkingHourType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', ChoiceType::class, [
                'choices' => DayOfWeekModel::getDayOfWeekMapping(),
                'label' => 'Working Hours',
                'attr' => [
                    'class' => 'working-hours-day',
                ],
                'multiple' => false,
                'required' => true,
                'choice_translation_domain' => true,
            ])
            ->add('timeStart', TimeType::class, array(
                'label' => 'Time Start',
                'input'  => 'datetime',
                'widget' => 'choice',
                'attr' => [
                    'class' => 'working-hours-time-start',
                ],
            ))
            ->add('timeEnd', TimeType::class, array(
                'label' => 'Time End',
                'input'  => 'datetime',
                'widget' => 'choice',
                'attr' => [
                    'class' => 'working-hours-time-end',
                ],
            ))
            ->add('openAllTime', CheckboxType::class, [
                'label' => 'Open All Time',
                'required' => false,
                'attr' => [
                    'class' => 'working-hours-open-all-time',
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Domain\BusinessBundle\Entity\BusinessProfileWorkingHour',
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain_business_bundle_business_profile_working_hour_type';
    }
}
