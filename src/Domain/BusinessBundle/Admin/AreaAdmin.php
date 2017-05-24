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
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'name',
    );

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
            ->add('updatedAt', 'doctrine_orm_datetime_range', $this->defaultDatagridDateTypeOptions)
            ->add('updatedUser')
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
            ->add('name')
            ->add('updatedAt', 'sonata_type_datetime_picker', [
                'required' => false,
                'disabled' => true
            ])
            ->add('updatedUser', 'sonata_type_model', [
                'required' => false,
                'btn_add' => false,
                'disabled' => true,
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
            ->add('name')
            ->add('businessProfiles')
            ->add('updatedAt')
            ->add('updatedUser')
        ;
    }
}
