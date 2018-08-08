<?php

namespace Domain\BusinessBundle\Form\Type;

use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('days', ChoiceType::class, [
                'choices' => DayOfWeekModel::getDayOfWeekMapping(),
                'label' => 'Working Hours',
                'attr' => [
                    'class' => 'form-control selectize-control select-multiple working-hours-day',
                    'placeholder' => 'working_hours.placeholder.days',
                ],
                'multiple' => true,
                'required' => true,
                'choice_translation_domain' => true,
            ])
            ->add('openAllTime', CheckboxType::class, [
                'label' => 'Open All Time',
                'required' => false,
                'attr' => [
                    'class' => 'working-hours-open-all-time',
                ],
            ])
            ->add('commentEn', TextType::class, [
                'required' => false,
                'label' => 'working_hours.label.comment_en',
            ])
            ->add('commentEs', TextType::class, [
                'required' => false,
                'label' => 'working_hours.label.comment_es',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var BusinessProfileWorkingHour $workingHours */
            $workingHours = $event->getData();
            $form = $event->getForm();

            if ($workingHours) {
                $timeStart = $workingHours->getTimeStart();
                $timeEnd   = $workingHours->getTimeEnd();
            } else {
                $timeStart = null;
                $timeEnd   = null;
            }

            $form
                ->add('timeStart', TextType::class, [
                    'label' => 'From',
                    'data' => DayOfWeekModel::getFormFormattedTime($timeStart),
                    'attr' => [
                        'class' => 'working-hours-time-start',
                        'type' => 'time',
                    ],
                ])
                ->add('timeEnd', TextType::class, [
                    'label' => 'Until',
                    'data' => DayOfWeekModel::getFormFormattedTime($timeEnd),
                    'attr' => [
                        'class' => 'working-hours-time-start',
                        'type' => 'time',
                    ],
                ])
            ;
        });
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
