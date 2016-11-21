<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class CategoryAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('parent.name')
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
            ->add('parent.name', null, ['label' => $this->trans('business.list.category_column', [], $this->getTranslationDomain())])
            ->add('name', null, ['label' => $this->trans('business.list.subcategory_column', [], $this->getTranslationDomain())])
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
            ->add('slug', null, ['read_only' => true, 'required' => false])
            ->add('articles', 'sonata_type_model', [
                'btn_add' => false,
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
            ])
            ->add('parent', 'sonata_type_model', [
                'btn_add' => false,
                'multiple' => false,
                'required' => false,
                'by_reference' => false,
            ])
            ->add('children', 'sonata_type_model', [
                'btn_add' => false,
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
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
            ->add('parent.name')
            ->add('slug')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection
            ->remove('delete_physical')
            ->add('delete_physical', null, [
                '_controller' => 'DomainBusinessBundle:CategoryAdminCRUD:deletePhysical'
            ])
        ;
    }

    public function prePersist($entity)
    {
        $this->preSave($entity);
    }

    public function preUpdate($entity)
    {
        $this->preSave($entity);
    }

    private function preSave($entity)
    {
        $textEn = '';
        $textEs = '';

        if ($entity->getLocale() == 'en') {
            $textEn = $entity->getName();

            if (!$entity->getSearchTextEs()) {
                $textEs = $entity->getName();
            }
        } else {
            $textEs = $entity->getName();

            if (!$entity->getSearchTextEn()) {
                $textEn = $entity->getName();
            }
        }

        if ($textEn) {
            $entity->setSearchTextEn($textEn);
        }

        if ($textEs) {
            $entity->setSearchTextEs($textEs);
        }
    }
}
