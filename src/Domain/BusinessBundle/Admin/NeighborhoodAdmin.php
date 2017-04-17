<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Neighborhood;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class NeighborhoodAdmin extends OxaAdmin
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
            ->add('name')
            ->add('locality')
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
            ->add('zips')
            ->add('locality')
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
            ->add('locality', null, [
                'required' => true,
            ])
            ->add(
                'zips',
                'sonata_type_collection',
                [
                    'by_reference' => false,
                    'required' => false,
                ],
                [
                    'edit' => 'inline',
                    'delete_empty' => false,
                    'inline' => 'table',
                ]
            )
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
            ->add('zips')
            ->add('locality')
            ->add('updatedAt')
            ->add('updatedUser')
        ;
    }
}
