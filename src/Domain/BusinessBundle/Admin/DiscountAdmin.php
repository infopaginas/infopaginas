<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class DiscountAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('businessProfile')
            ->add('status', 'doctrine_orm_choice', AdminHelper::getDatagridStatusOptions())
            ->add('coupon')
            ->add('description')
            ->add('value')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('businessProfile')
            ->add('coupon')
            ->add('statusValue', null, ['label' => 'Status'])
            ->add('value')
            //->add('statusValue')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // to show record Id in sonata_type_collection form type
        if ($this->getRoot()->getClass() != $this->getClass()) {
            $formMapper
                ->add('id', 'text', [
                    'read_only' => true,
                    'mapped' => false,
                    'disabled' => true,
                    'data' => ($this->getSubject()) ? $this->getSubject()->getId() : null
                ])
            ;
        }

        $formMapper
            ->add('status', 'choice', ['choices' => StatusTrait::getStatuses()])
            ->add('businessProfile', null, [
                // hide this field if this page used as sonata_type_collection on other pages
                'attr' => ['hidden' => $this->getRoot()->getClass() != $this->getClass()]
            ])
            ->add('coupon')
            ->add('description')
            ->add('value')
            ->add('startDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
            ->add('endDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('businessProfile')
            ->add('coupon')
            ->add('description')
            ->add('value')
            ->add('statusValue')
        ;
    }
}
