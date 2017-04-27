<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
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
            ->add('slug', null, ['read_only' => true, 'required' => false])
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
            ->add('slug')
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

    /**
     * @param Category $entity
     */
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

    /**
     * @param string $name
     * @param null $object
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if ($object and in_array($name, $deniedActions) and (!$object->getBusinessProfiles()->isEmpty()
            or in_array($object->getCode(), Category::getDefaultCategories()) or !$object->getArticles()->isEmpty())
        ) {
            return false;
        }

        return parent::isGranted($name, $object);
    }
}
