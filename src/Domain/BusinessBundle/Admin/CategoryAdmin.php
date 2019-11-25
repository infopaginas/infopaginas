<?php

namespace Domain\BusinessBundle\Admin;

use Domain\ArticleBundle\Entity\Article;
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
            ->add('showSuggestion')
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
            ->add('showSuggestion')
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
            ->add('showSuggestion')
            ->add('slug', null, ['read_only' => true, 'required' => false])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $showMapper
            ->with('Category')
                ->add('id')
                ->add('name')
                ->add('showSuggestion')
                ->add('slug')
            ->end()
            ->with('Dependencies')
                ->add('businessProfilesLimited', null, [
                    'label' => 'Business Profiles',
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many_w_link.html.twig',
                    'data' => [
                        'path' => 'admin_domain_business_businessprofile_edit',
                        'value' => $container->get('doctrine')
                            ->getRepository(BusinessProfile::class)
                            ->getBusinessByCategory($this->getSubject(), Category::RELATED_ENTITIES_DISPLAY_COUNT),
                        'count' => $container->get('doctrine')
                            ->getRepository(BusinessProfile::class)->getBusinessCountForCategory($this->getSubject()),
                    ],
                ])
                ->add('articlesLimited', null, [
                    'template' => 'OxaSonataAdminBundle:ShowFields:show_orm_one_to_many_w_link.html.twig',
                    'data' => [
                        'path' => 'admin_domain_article_article_edit',
                        'value' => $container->get('doctrine')
                            ->getRepository(Article::class)
                            ->getArticlesByCategory($this->getSubject(), Category::RELATED_ENTITIES_DISPLAY_COUNT),
                        'count' => $container->get('doctrine')
                            ->getRepository(Article::class)->getArticlesCountForCategory($this->getSubject()),
                    ],
                ])
            ->end()
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
        parent::preUpdate($entity);
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
     * @param Category|null $object
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if ($object and in_array($name, $deniedActions) and
            (in_array($object->getCode(), Category::getDefaultCategories()) or
                !$object->getBusinessProfiles()->isEmpty() or
                !$object->getArticles()->isEmpty())
        ) {
            return false;
        }

        return parent::isGranted($name, $object);
    }

    public function configureBatchActions($actions)
    {
        unset($actions['delete']);

        return $actions;
    }
}
