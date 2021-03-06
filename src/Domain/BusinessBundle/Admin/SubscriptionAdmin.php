<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SubscriptionAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('businessProfile.name')
            ->add('subscriptionPlan')
            ->add('status', ChoiceFilter::class, AdminHelper::getDatagridStatusOptions())
            ->add('startDate', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
            ->add('endDate', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
            ->add('createdAt', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
            ->add('updatedAt', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
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
            ->add('statusValue', null, ['label' => 'Status'])
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

        // to show record Id in sonata_type_collection form type
        if ($this->getRoot()->getClass() != $this->getClass()) {
            $formMapper
                ->with('General')
                    ->add('id', TextType::class, [
                        'attr' => [
                            'read_only' => true,
                        ],
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
            'disabled' => true,
        ];

        $formMapper
            ->with('General')
                ->add('businessProfile')
                ->add('status', ChoiceType::class, ['choices' => array_flip(StatusTrait::getStatuses())])
                ->add('subscriptionPlan')
            ->end()
            ->with('Period')
                ->add('startDate', DateTimePickerType::class, [
                    'format' => self::FORM_DATETIME_FORMAT,
                    'attr' => [
                        'class' => 'start-date',
                    ],
                ])
                ->add('endDate', DateTimePickerType::class, [
                    'format' => self::FORM_DATETIME_FORMAT,
                    'attr' => [
                        'class' => 'end-date',
                    ],
                ])
            ->end()
            ->with('Status')
                ->add('createdAt', DateTimePickerType::class, $systemDatetimeOptions)
                ->add('createdUser', TextType::class, $systemUserOptions)
                ->add('updatedAt', DateTimePickerType::class, $systemDatetimeOptions)
                ->add('updatedUser', TextType::class, $systemUserOptions)
            ->end()
        ;

        // remove this field if this page used as sonata_type_collection on other pages
        if ($this->getRoot()->getClass() != $this->getClass()) {
            $formMapper->remove('businessProfile');
        }
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
     * @param Subscription $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        if ($object->getStartDate() > new \DateTime('now')) {
            $errorElement->with('startDate')
                ->addViolation($this->getTranslator()->trans(
                    'form.subscription.start_date',
                    [],
                    $this->getTranslationDomain()
                ))
                ->end()
            ;
        }
    }
}
