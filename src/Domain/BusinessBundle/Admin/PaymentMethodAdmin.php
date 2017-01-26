<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PaymentMethodAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
        ;

        // remove businessProfiles field if we create object on businessProfile edit page
        $parentCode = $this->getRequest()->get('pcode');
        $businessProfileCode = $this->getConfigurationPool()
            ->getContainer()
            ->get('domain_business.admin.business_profile')
            ->getCode();

        if ($parentCode && $parentCode == $businessProfileCode) {
            $formMapper->remove('businessProfiles');
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
        ;
    }

    /**
     * @param string $name
     * @param null $object
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if ($object && in_array($name, $deniedActions) &&
            in_array(strtolower($object->getType()), $object::getRequiredPaymentMethods())
        ) {
            return false;
        }

        return parent::isGranted($name, $object);
    }
}
