<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AreaAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name');

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('businessProfiles', 'sonata_type_model', [
                'btn_add' => false,
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
            ]);;

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
            ->add('businessProfiles');
    }
}
