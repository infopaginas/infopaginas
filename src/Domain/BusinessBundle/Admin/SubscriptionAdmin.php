<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Subscription;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class SubscriptionAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('businessProfile')
            ->add('subscriptionPlan')
            ->add('startDate', 'doctrine_orm_datetime_range', [
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => [
                    'format' => self::FILTER_DATETIME_FORMAT
            ]])
            ->add('endDate', 'doctrine_orm_datetime_range', [
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => [
                    'format' => self::FILTER_DATETIME_FORMAT
            ]])
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
            ->add('subscriptionPlan')
            ->add('startDate')
            ->add('endDate')
            ->add('createdAt')
            ->add('createdUser')
            ->add('updatedAt')
            ->add('updatedUser')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-4'))->end()
            ->with('Period', array('class' => 'col-md-4'))->end()
            ->with('Status', array('class' => 'col-md-4'))->end()
        ;

        if ($this->getRoot()->getClass() != $this->getClass()) {
            $formMapper
                ->with('General')
                    ->add('subscriptionId', 'text', [
                        'read_only' => true,
                        'mapped' => false,
                        'data' => ($this->getSubject()) ? $this->getSubject()->getId() : null
                    ])
                ->end();
        }

        $formMapper
            ->with('General')
                ->add('businessProfile', null, [
                    // hide this field if this page used as sonata_type_collection on other pages
                    'attr' => ['hidden' => $this->getRoot()->getClass() != $this->getClass()]
                ])
                ->add('subscriptionPlan')
            ->end()
            ->with('Period')
                ->add('startDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
                ->add('endDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
            ->end()
            ->with('Status')
                ->add('createdAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                ->add('createdUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
                ->add('updatedAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                ->add('updatedUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
            ->end()
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
            ->add('subscriptionPlan')
            ->add('startDate')
            ->add('endDate')
            ->add('createdAt')
            ->add('createdUser')
            ->add('updatedAt')
            ->add('updatedUser')
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        if ($object instanceof Subscription && $object->getStartDate()) {
            if ($object->getStartDate()->diff($object->getEndDate())->invert) {
                $errorElement->with('endDate')
                    ->addViolation('End Date must be later than Start Date')
                    ->end()
                ;
            }
        }
    }
}
