<?php

namespace Domain\BusinessBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\SearchBundle\Util\CacheUtil;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Filter\CaseInsensitiveStringFilter;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;

class LocalityAdmin extends OxaAdmin
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
            ->add('name', CaseInsensitiveStringFilter::class, [
                'show_filter' => true,
            ])
            ->add('area')
            ->add('updatedAt', DateTimeRangeFilter::class, $this->defaultDatagridDateTypeOptions)
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
            ->add('latitude')
            ->add('longitude')
            ->add('area')
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
            ->add('latitude')
            ->add('longitude')
            ->add('area', null, [
                'required' => true,
            ])
            ->add(
                'pseudos',
                CollectionType::class,
                [
                    'by_reference'  => false,
                    'required'      => false,
                    'btn_add'       => false,
                    'type_options' => [
                        'delete'    => false,
                    ],
                    'attr' => [
                        'read_only'     => true,
                    ],
                ],
                [
                    'edit'          => 'inline',
                    'delete_empty'  => false,
                    'inline'        => 'table',
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
            ->add('latitude')
            ->add('longitude')
            ->add('area')
            ->add('updatedAt')
            ->add('updatedUser')
            ->add('pseudos')
        ;
    }

    /**
     * @param Locality $entity
     */
    public function preRemove($entity)
    {
        $this->replaceBusinessCatalogLocality($entity);
    }

    /**
     * @param Locality $entity
     */
    public function postRemove($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postRemove($entity);
    }

    /**
     * @param Locality $entity
     */
    public function postPersist($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postPersist($entity);
    }

    /**
     * @param Locality $entity
     */
    public function postUpdate($entity)
    {
        $this->invalidateLandingShortcutCache();
        parent::postUpdate($entity);
    }

    /**
     * @param string $name
     * @param null $object
     *
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if ($object && in_array($name, $deniedActions) &&
            $object->getSlug() == Locality::DEFAULT_CATALOG_LOCALITY_SLUG
        ) {
            return false;
        }

        return parent::isGranted($name, $object);
    }

    /**
     * @param Locality $entity
     */
    protected function replaceBusinessCatalogLocality($entity)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');

        $defaultLocality = $em->getRepository(Locality::class)
            ->getLocalityBySlug(Locality::DEFAULT_CATALOG_LOCALITY_SLUG);

        if ($defaultLocality) {
            /* get businesses by catalog locality */
            $businesses = $entity->getBusinessProfiles();

            $this->updateBusinessProfiles($businesses, $defaultLocality, $entity);

            /* get businesses by service area locality */
            $businesses = $entity->getBusinessProfile();

            $this->updateBusinessProfiles($businesses, $defaultLocality, $entity);
        }
    }

    /**
     * @param ArrayCollection $businesses
     * @param Locality $defaultLocality
     * @param Locality $entity
     */
    protected function updateBusinessProfiles($businesses, $defaultLocality, $entity)
    {
        foreach ($businesses as $businessProfile) {
            /** @var BusinessProfile $businessProfile */
            $businessProfile->setCatalogLocality($defaultLocality);

            if (!$businessProfile->getLocalities()->contains($defaultLocality)) {
                $businessProfile->addLocality($defaultLocality);
            }

            $businessProfile->removeLocality($entity);
        }
    }

    private function invalidateLandingShortcutCache(): void
    {
        $memcachedCache = $this->getConfigurationPool()->getContainer()->get('app.cache.memcached');
        CacheUtil::invalidateCacheByPrefix($memcachedCache, CacheUtil::PREFIX_HOMEPAGE_SHORTCUT . 'prefix');
    }
}
