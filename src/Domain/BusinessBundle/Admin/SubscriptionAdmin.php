<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\Validator\ConstraintViolation;

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
            ->add('status', 'doctrine_orm_choice', [
                'field_type' => 'choice',
                'field_options' => [
                    'required'  => false,
                    'choices'   => StatusTrait::getStatuses()
                ]
            ])
            ->add('startDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
            ->add('endDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
            ->add('createdAt', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
            ->add('updatedAt', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
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
            ->add('statusValue')
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
                        'disabled' => true,
                        'data' => ($this->getSubject()) ? $this->getSubject()->getId() : null
                    ])
                ->end();
        }

        // put this in admin helper
        $systemDatetimeOptions = [
            'format' => self::FORM_DATETIME_FORMAT,
            'required' => false,
            'disabled' => true
        ];

        $systemUserOptions = [
            'required' => false,
            'btn_add' => false,
            'disabled' => true,
        ];

        $formMapper
            ->with('General')
                ->add('businessProfile', null, [
                    // hide this field if this page used as sonata_type_collection on other pages
                    'attr' => ['hidden' => $this->getRoot()->getClass() != $this->getClass()]
                ])
                ->add('status', 'choice', ['choices' => StatusTrait::getStatuses()])
                ->add('subscriptionPlan')
            ->end()
            ->with('Period')
                ->add('startDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
                ->add('endDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
            ->end()
            ->with('Status')
                ->add('createdAt', 'sonata_type_datetime_picker', $systemDatetimeOptions)
                ->add('createdUser', 'sonata_type_model', $systemUserOptions)
                ->add('updatedAt', 'sonata_type_datetime_picker', $systemDatetimeOptions)
                ->add('updatedUser', 'sonata_type_model', $systemUserOptions)
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

//    /**
//     * @param ErrorElement $errorElement
//     * @param mixed $object
//     */
//    public function validate(ErrorElement $errorElement, $object)
//    {
//        if ($object instanceof Subscription && $object->getStartDate()) {
//                if ($object->getStartDate()->diff($object->getEndDate())->invert) {
//                $errorElement->with('endDate')
//                    ->addViolation('End Date must be later than Start Date')
//                    ->end()
//                ;
//            }
//        }
//    }
}
