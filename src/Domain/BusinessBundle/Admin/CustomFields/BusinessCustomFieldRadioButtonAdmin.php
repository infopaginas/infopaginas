<?php

namespace Domain\BusinessBundle\Admin\CustomFields;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonCollection;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BusinessCustomFieldRadioButtonAdmin extends OxaAdmin
{
    const MAX_BUSINESS_NAMES_SHOW = 10;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('hideTitle')
            ->add('section')
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
            ->add('hideTitle')
            ->add('section')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Radio Button')
            ->add('title', null, ['required' => true])
            ->add('hideTitle')
            ->add('section', null, ['required' => true])
            ->add(
                'radioButtonItems',
                'sonata_type_collection',
                [
                    'by_reference' => false,
                    'required'     => true,
                ],
                [
                    'edit'         => 'inline',
                    'delete_empty' => false,
                    'inline'       => 'table',
                    'sortable'     => 'position',
                ]
            )
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
            ->add('hideTitle')
            ->add('section')
            ->add('radioButtonItems')
        ;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('move', $this->getRouterIdParameter().'/move/{position}')
        ;
    }

    public function getDependentBusinessNames()
    {
        $checkboxCollectionRepository = $this->getConfigurationPool()->getContainer()->get('doctrine')
            ->getRepository(BusinessCustomFieldRadioButtonCollection::class);

        $businessNames = $checkboxCollectionRepository->getBusinessProfileNames($this->getSubject()->getId());

        return $businessNames;
    }

    public function getDependentBusinessCount()
    {
        $checkboxCollectionRepository = $this->getConfigurationPool()->getContainer()->get('doctrine')
            ->getRepository(BusinessCustomFieldRadioButtonCollection::class);

        $businessCount = $checkboxCollectionRepository->countBusinesses($this->getSubject()->getId());

        return ($businessCount > self::MAX_BUSINESS_NAMES_SHOW) ? true : false;
    }

    public function configure()
    {
        $this->setPerPageOptions([10, 25, 50, 100, 250, 500]);

        $this->setTemplate('delete', 'OxaSonataAdminBundle:CRUD:custom_field_delete.html.twig');
    }
}
