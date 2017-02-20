<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Model\DayOfWeekModel;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BusinessProfileWorkingHourAdmin extends OxaAdmin
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
            ->add('day')
            ->add('from')
            ->add('to')
            ->add('isOpen')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('day', ChoiceType::class, [
                'choices' => DayOfWeekModel::getDayOfWeekMapping(),
                'multiple' => false,
                'required' => true,
            ])
            ->add('timeStart')
            ->add('timeEnd')
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
            ->add('day')
            ->add('timeStart')
            ->add('timeEnd')
            ->add('openAllTime')
        ;
    }
}
