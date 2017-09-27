<?php

namespace Domain\EmergencyBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourTypeValidator;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\EqualType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EmergencyBusinessAdmin
 * @package Domain\EmergencyBundle\Admin
 */
class EmergencyBusinessAdmin extends OxaAdmin
{
    /**
     * @return EmergencyBusiness
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $container = $this->getConfigurationPool()->getContainer();

        if ($this->getRequest()->getMethod() == Request::METHOD_GET) {
            $parentId = $this->getRequest()->get('id', null);

            if ($parentId) {
                $parent = $container->get('doctrine')->getRepository(BusinessProfile::class)->find($parentId);

                if ($parent) {
                    $instance = $this->cloneParentEntity($parent, $instance);
                }
            }
        }

        return $instance;
    }

    /**
     * @param BusinessProfile   $parent
     * @param EmergencyBusiness $instance
     *
     * @return EmergencyBusiness
     */
    protected function cloneParentEntity(BusinessProfile $parent, $instance)
    {
        $instance->setName($parent->getName());
        $instance->setAddress($parent->getFullAddress());

        if (!$parent->getPhones()->isEmpty()) {
            $mainPhone = $parent->getMainPhone();

            if ($mainPhone) {
                $phone = $mainPhone;
            } else {
                $phone = $parent->getPhones()->first();
            }

            $instance->setPhone($phone->getPhone());
        }

        return $instance;
    }

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
        ;
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
            ->add('updatedAt')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Main')
                ->add('name')
            ->end()
            ->with('Working hours')
                ->add(
                    'collectionWorkingHours',
                    'sonata_type_collection',
                    [
                        'by_reference'  => false,
                        'required'      => true,
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
                ->add('area', 'sonata_type_model_list', [
                    'required'      => true,
                    'btn_delete'    => false,
                    'btn_add'       => false,
                ])
                ->add('category', 'sonata_type_model_list', [
                    'required'      => true,
                    'btn_delete'    => false,
                    'btn_add'       => false,
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
            ->end()
        ;
    }
}
