<?php

namespace Domain\BusinessBundle\Admin\CustomFields;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListCollection;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Traits\ShowBusinessNamesOnDeleteTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BusinessCustomFieldListAdmin extends OxaAdmin
{
    use ShowBusinessNamesOnDeleteTrait;

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
            ->tab('List')
            ->add('title', null, ['required' => true])
            ->add('hideTitle')
            ->add('section', null, ['required' => true])
            ->add(
                'listItems',
                CollectionType::class,
                [
                    'by_reference'  => false,
                    'required'      => true,
                ],
                [
                    'edit'          => 'inline',
                    'delete_empty'  => false,
                    'inline'        => 'table',
                    'sortable'      => 'position',
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
            ->add('listItems')
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

    private function getCollectionRepository()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine')
            ->getRepository(BusinessCustomFieldListCollection::class);
    }
}
