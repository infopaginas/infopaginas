<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
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
            ->add('status', 'doctrine_orm_choice', [
                'field_type' => 'choice',
                'field_options' => [
                    'required'  => false,
                    'choices'   => StatusTrait::getStatuses()
                ]
            ])
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
            ->add('value')
            ->add('statusValue')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        if ($this->getRoot()->getClass() != $this->getClass()) {
            $formMapper
                ->add('discountId', 'text', [
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
            ->add('value', null, [
                'required' => true
            ])
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
