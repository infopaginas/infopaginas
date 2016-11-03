<?php

namespace Domain\BannerBundle\Admin;

use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CampaignAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('status', 'doctrine_orm_choice', AdminHelper::getDatagridStatusOptions())
            ->add('startDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
            ->add('endDate', 'doctrine_orm_datetime_range', $this->defaultDatagridDatetimeTypeOptions)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('businessProfile')
            ->add('statusValue', null, ['label' => 'Status'])
            ->add('startDate')
            ->add('endDate')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))->end()
            ->with('Period', array('class' => 'col-md-6'))->end()
            ->with('Banners', array('class' => 'col-md-12'))->end()
        ;

        $formMapper
            ->with('General')
                ->add('title')
                ->add('businessProfile')
                ->add('updatedAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                ->add('updatedUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
                ->add('createdAt', 'sonata_type_datetime_picker', ['required' => false, 'disabled' => true])
                ->add('createdUser', 'sonata_type_model', [
                    'required' => false,
                    'btn_add' => false,
                    'disabled' => true,
                ])
            ->end()
            ->with('Period')
                ->add('status', 'choice', ['choices' => StatusTrait::getStatuses()])
                ->add('startDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
                ->add('endDate', 'sonata_type_datetime_picker', ['format' => self::FORM_DATETIME_FORMAT])
            ->end()
            ->with('Banners')
                ->add('banners', 'sonata_type_collection', [
                    'type_options' => [
                        'delete' => true,
                        'delete_options' => [
                            'type' => 'checkbox',
                            'type_options' => ['mapped' => false, 'required' => false]
                        ]]
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'allow_delete' => false,
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
            ->add('title')
            ->add('startDate')
            ->add('endDate')
        ;
    }
}
