<?php

namespace Domain\EmergencyBundle\Admin;

use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Form\Type\GoogleMapType;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourTypeValidator;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\EmergencyBundle\Entity\EmergencyDraftBusiness;
use Domain\ReportBundle\Model\UserActionModel;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class EmergencyDraftBusinessAdmin
 * @package Domain\EmergencyBundle\Admin
 */
class EmergencyDraftBusinessAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, [
                'show_filter' => true,
            ])
            ->add('area')
            ->add('category')
            ->add('address')
            ->add('phone')
            ->add('paymentMethods')
            ->add('services')
            ->add('status', null, [
                'field_options' => [
                    'choices' => EmergencyDraftBusiness::getStatuses(),
                ],
                'field_type' => ChoiceType::class,
            ])
        ;
    }

    /**
     * @return array
     */
    public function getFilterParameters()
    {
        $this->datagridValues['_sort_by']    = 'updatedAt';
        $this->datagridValues['_sort_order'] = 'DESC';
        $this->datagridValues['status']['value'] = EmergencyDraftBusiness::STATUS_PENDING;

        return parent::getFilterParameters();
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('area', null, [
                'sortable' => true,
                'sort_field_mapping' => [
                    'fieldName' => 'name',
                ],
                'sort_parent_association_mappings' => [
                    [
                        'fieldName' => 'area',
                    ],
                ]
            ])
            ->add('category', null, [
                'sortable' => true,
                'sort_field_mapping' => [
                    'fieldName' => 'name',
                ],
                'sort_parent_association_mappings' => [
                    [
                        'fieldName' => 'category',
                    ],
                ]
            ])
            ->add('address')
            ->add('phone')
            ->add('status')
            ->add('updatedAt')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $draft = $this->getSubject();

        $formMapper
            ->with('Main')
                ->add('name')
            ->end()
            ->with('Working hours')
                ->add('customWorkingHours', null, [
                    'help' => 'emergency.custom_working_hour.help',
                ])
                ->add(
                    'collectionWorkingHours',
                    CollectionType::class,
                    [
                        'by_reference'  => false,
                        'required'      => false,
                    ],
                    [
                        'edit'          => 'inline',
                        'delete_empty'  => false,
                        'inline'        => 'table',
                    ]
                )
                ->add(BusinessProfileWorkingHourTypeValidator::ERROR_BLOCK_PATH, TextType::class, [
                    'label_attr' => [
                        'hidden' => true,
                    ],
                    'mapped'   => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'hidden',
                    ],
                ])
            ->end()
            ->with('Address')
                ->add('address')
                ->add('phone')
            ->end()
            ->with('Payments method')
                ->add('paymentMethods', null, [
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->end()
            ->with('Services')
                ->add('services', null, [
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->end()
            ->with('Catalog')
                ->add('area', ModelListType::class, [
                    'required'      => true,
                    'btn_delete'    => false,
                    'btn_add'       => false,
                    'model_manager' => $this->modelManager,
                    'class'         => EmergencyArea::class,
                ])
                ->add('category', ModelListType::class, [
                    'required'      => true,
                    'btn_delete'    => false,
                    'btn_add'       => false,
                    'model_manager' => $this->modelManager,
                    'class'         => EmergencyCategory::class,
                ])
                ->add('customCategory')
            ->end()
        ;

        // Map Block
        $oxaConfig = $this->getConfigurationPool()->getContainer()->get('oxa_config');

        if ($draft->getLatitude() and $draft->getLongitude()) {
            $latitude   = $draft->getLatitude();
            $longitude  = $draft->getLongitude();
        } else {
            $latitude   = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LATITUDE);
            $longitude  = $oxaConfig->getValue(ConfigInterface::DEFAULT_MAP_COORDINATE_LONGITUDE);
        }

        $formMapper
            ->with('Map')
                ->add('latitude')
                ->add('longitude')
                ->add('map', GoogleMapType::class, [
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                    'mapped'    => false,
                ])
            ->end()
        ;

        $formMapper
            ->with('Status')
                ->add('status', ChoiceType::class, [
                    'choices' => EmergencyDraftBusiness::getStatuses(),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
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
            ->with('Main')
                ->add('name')
            ->end()
            ->with('Working hours')
                ->add('collectionWorkingHours', null, [
                    'template' => 'DomainBusinessBundle:Admin:BusinessProfile/show_working_hours_collection.html.twig',
                ])
                ->add('customWorkingHours')
            ->end()
            ->with('Address')
                ->add('address')
                ->add('phone')
            ->end()
            ->with('Payments method')
                ->add('paymentMethods', null, [
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                ])
            ->end()
            ->with('Services')
                ->add('services', null, [
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_many.html.twig',
                ])
            ->end()
            ->with('Catalog')
                ->add('area', null, [
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                ])
                ->add('category', null, [
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_many_to_one.html.twig',
                ])
                ->add('customCategory')
            ->end()
            ->with('Map')
                ->add('latitude')
                ->add('longitude')
                ->add('map')
            ->end()
        ;
    }

    /**
     * @param EmergencyDraftBusiness $draft
     */
    public function postUpdate($draft)
    {
        $status = $draft->getStatus();

        if ($status == EmergencyDraftBusiness::STATUS_APPROVED) {
            $this->approveDraft($draft);
        } elseif ($status == EmergencyDraftBusiness::STATUS_REJECTED) {
            $this->rejectDraft($draft);
        } else {
            parent::postUpdate($draft);
        }
    }

    /**
     * @param EmergencyDraftBusiness $draft
     */
    protected function approveDraft($draft)
    {
        $this->handleActionLog(
            UserActionModel::TYPE_ACTION_DRAFT_APPROVE,
            $draft
        );

        $business = $this->createEmergencyBusinessFromDraft($draft);

        $this->handleActionLog(
            UserActionModel::TYPE_ACTION_CREATE,
            $business
        );
    }

    /**
     * @param EmergencyDraftBusiness $draft
     */
    protected function rejectDraft($draft)
    {
        $this->handleActionLog(
            UserActionModel::TYPE_ACTION_DRAFT_REJECT,
            $draft
        );
    }

    /**
     * @param EmergencyDraftBusiness $draft
     *
     * @return EmergencyBusiness
     */
    protected function createEmergencyBusinessFromDraft($draft)
    {
        $business = new EmergencyBusiness();

        $business->setName($draft->getName());
        $business->setAddress($draft->getAddress());
        $business->setPhone($draft->getPhone());
        $business->setCategory($draft->getCategory());
        $business->setArea($draft->getArea());

        $business->setLatitude($draft->getLatitude());
        $business->setLongitude($draft->getLongitude());

        foreach ($draft->getPaymentMethods() as $paymentMethod) {
            $business->addPaymentMethod($paymentMethod);
        }

        foreach ($draft->getServices() as $service) {
            $business->addService($service);
        }

        foreach ($draft->getCollectionWorkingHours() as $collectionWorkingHours) {
            $workingHours = clone $collectionWorkingHours;

            $business->addCollectionWorkingHour($workingHours);
        }

        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $em->persist($business);
        $em->flush($business);

        return $business;
    }

    /**
     * @param string $name
     * @param EmergencyDraftBusiness $draft
     * @return bool
     */
    public function isGranted($name, $draft = null)
    {
        $deniedActions = $this->getEditDeniedAction();

        if ($draft and in_array($name, $deniedActions) and
            $draft->getStatus() == EmergencyDraftBusiness::STATUS_APPROVED
        ) {
            return false;
        }

        return parent::isGranted($name, $draft);
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('export')
        ;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return EmergencyDraftBusiness::getExportFormats();
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $exportFields['ID']         = 'id';
        $exportFields['Name']       = 'name';
        $exportFields['Area']       = 'area.name';
        $exportFields['Category']   = 'category.name';
        $exportFields['Address']    = 'address';
        $exportFields['Phone']      = 'phone';
        $exportFields['UpdatedAt']  = 'updatedAt';
        $exportFields['WorkingHours'] = 'exportWorkingHours';
        $exportFields['PaymentsMethods'] = 'exportPaymentsMethods';
        $exportFields['Services'] = 'exportServices';
        $exportFields['Status']   = 'status';
        $exportFields['CustomCategory'] = 'customCategory';
        $exportFields['CustomWorkingHours'] = 'customWorkingHours';

        return $exportFields;
    }
}
