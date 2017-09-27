<?php

namespace Domain\EmergencyBundle\Admin;

use Domain\BusinessBundle\Model\DayOfWeekModel;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EmergencyBusinessWorkingHourAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('timeStart')
            ->add('timeEnd')
            ->add('openAllTime')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $workingHours = $this->getSubject();

        if ($workingHours) {
            $timeStart = $workingHours->getTimeStart();
            $timeEnd   = $workingHours->getTimeEnd();
        } else {
            $timeStart = null;
            $timeEnd   = null;
        }

        $formMapper
            ->add('days', ChoiceType::class, [
                'choices' => DayOfWeekModel::getDayOfWeekMapping(),
                'multiple' => true,
                'required' => true,
            ])
            ->add('timeStart', TextType::class, [
                'label' => 'Time Start',
                'required' => false,
                'data' => DayOfWeekModel::getFormFormattedTime($timeStart),
                'attr' => [
                    'class' => 'working-hours-time-start',
                    'type' => 'time',
                ],
            ])
            ->add('timeEnd', TextType::class, [
                'label' => 'Time End',
                'required' => false,
                'data' => DayOfWeekModel::getFormFormattedTime($timeEnd),
                'attr' => [
                    'class' => 'working-hours-time-start',
                    'type' => 'time',
                ],
            ])
            ->add('openAllTime')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('timeStart')
            ->add('timeEnd')
            ->add('openAllTime')
        ;
    }
}
