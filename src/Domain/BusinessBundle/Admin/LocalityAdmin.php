<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
            ->add('name')
            ->add('area')
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
            ->add('area')
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
        ;
    }

    public function preRemove($entity)
    {
        $this->replaceBusinessCatalogLocality($entity);
    }

    /**
     * @param string $name
     * @param null $object
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = [
            'DELETE',
            'ROLE_PHYSICAL_DELETE_ABLE',
            'ROLE_RESTORE_ABLE',
        ];

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

        $defaultLocality = $em->getRepository('DomainBusinessBundle:Locality')
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
}
