<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
                'show_filter' => true,
            ])
            ->add('searchTextEs', null, [
                'label' => 'Name Esp',
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

    /**
     * @param Category $entity
     */
    public function prePersist($entity)
    {
        $this->preSave($entity);
    }

    /**
     * @param Category $entity
     */
    public function preUpdate($entity)
    {
        $this->preSave($entity);
    }

    /**
     * @param Category $entity
     */
    private function preSave($entity)
    {
        if ($entity->getName()) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $currentLocalePostfix  = LocaleHelper::getLangPostfix($entity->getLocale());
            $currentPropertyLocale = Category::CATEGORY_LOCALE_PROPERTY . $currentLocalePostfix;

            if (property_exists($entity, $currentPropertyLocale)) {
                $accessor->setValue($entity, $currentPropertyLocale, $entity->getName());
            }

            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $localePostfix  = LocaleHelper::getLangPostfix($locale);
                $propertyLocale = Category::CATEGORY_LOCALE_PROPERTY . $localePostfix;

                if (property_exists($entity, $propertyLocale) and !$accessor->getValue($entity, $propertyLocale)) {
                    $accessor->setValue($entity, $propertyLocale, $entity->getName());
                }
            }
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

        if ($object and in_array($name, $deniedActions) and
            (in_array($object->getCode(), Category::getDefaultCategories()) or
            $object->getSlugEn() or $object->getSlugEs())) {
            return false;
        }

        return parent::isGranted($name, $object);
    }
}
