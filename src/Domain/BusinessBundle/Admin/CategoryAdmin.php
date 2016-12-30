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
            ->add('name', null, [
                'label' => $this->trans('business.list.category_column', [], $this->getTranslationDomain())
            ])
            ->add('parent.name', null, [
                'label' => $this->trans('business.list.parent_category_column', [], $this->getTranslationDomain())
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name', null, [
                'label' => $this->trans('business.list.category_column', [], $this->getTranslationDomain())
            ])
            ->add('parent', null, [
                'label' => $this->trans('business.list.parent_category_column', [], $this->getTranslationDomain()),
                'sortable' => true,
                'sort_field_mapping'=> ['fieldName' => 'name'],
                'sort_parent_association_mappings' => [['fieldName' => 'parent']]
            ])
            ->add('categoryType', null, [
                'label' => $this->trans('business.list.category_type', [], $this->getTranslationDomain()),
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/list_category_type.html.twig'
            ])
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $category = $this->getSubject();

        $em = $this->modelManager->getEntityManager(Category::class);

        $lvl = $category->getLvl();

        if ($lvl == Category::CATEGORY_LEVEL_1 and !$category->getBusinessProfiles()->isEmpty()) {
            $parentLvl = 0;
        } elseif($lvl) {
            $maxLevel = $em->createQueryBuilder('c')
                ->select('MAX(c.lvl)')
                ->from(Category::class, 'c')
                ->andWhere('c.path LIKE :path')
                ->setParameter('path', $category->getPath() . '%')
                ->getQuery()
                ->getSingleScalarResult()
            ;

            $levelDiff = $maxLevel - $lvl;

            if ($levelDiff == 0) {
                $parentLvl = Category::CATEGORY_LEVEL_2;
            } elseif ($levelDiff == 1) {
                $parentLvl = Category::CATEGORY_LEVEL_1;
            } else {
                $parentLvl = 0;
            }
        } else {
            $parentLvl = Category::CATEGORY_LEVEL_2;
        }

        $parentQuery = $this->modelManager->createQuery(Category::class, 'c')
            ->where('c.isActive = TRUE')
            ->andWhere('c.lvl <= :maxLevel')
            ->setParameter('maxLevel', $parentLvl)
            ->orderBy('c.name')
        ;

        if ($category->getId()) {
            $parentQuery
                ->andWhere('c.id != :id')
                ->setParameter('id', $category->getId())
            ;
        }

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
                'label' => $this->trans('business.list.parent_category_column', [], $this->getTranslationDomain()),
                'attr' => [
                    'disabled' => $parentLvl ? false : true,
                ],
                'query' => $parentQuery,
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
            ->add('name', null, [
                'label' => $this->trans('business.list.category_column', [], $this->getTranslationDomain())
            ])
            ->add('parent.name', null, [
                'label' => $this->trans('business.list.parent_category_column', [], $this->getTranslationDomain())
            ])
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

        //remove category form business only for lvl 2 and 3 (lvl 1 is forbidden to update if binded to business)
        $businesses = $entity->getBusinessProfiles();

        if (!$businesses->isEmpty()) {
            $parentCategory = $entity->getParent();
            $childrenCategory = $entity->getChildren();

            foreach ($businesses as $business) {
                /* @var $business BusinessProfile */
                if ($parentCategory) {
                    if (!$business->getCategories()->contains($parentCategory)) {
                        $business->removeCategory($entity);

                        foreach ($childrenCategory as $child) {
                            if ($business->getCategories()->contains($child)) {
                                $business->removeCategory($child);
                            }
                        }
                    }
                } else {
                    $categories = $business->getCategories();

                    foreach ($categories as $category) {
                        if ($category->getLvl() == Category::CATEGORY_LEVEL_1 and $category != $entity) {
                            $business->removeCategory($entity);
                            break;
                        }
                    }
                }
            }
        }
    }
}
