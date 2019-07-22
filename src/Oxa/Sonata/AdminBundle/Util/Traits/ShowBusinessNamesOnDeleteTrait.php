<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

trait ShowBusinessNamesOnDeleteTrait
{
    public function getDependentBusinessNames()
    {
        return $this->getCollectionRepository()->getBusinessProfileNames($this->getSubject()->getId());
    }

    public function getDependentBusinessCount()
    {
        $businessCount = $this->getCollectionRepository()->countBusinesses($this->getSubject()->getId());

        return ($businessCount > self::MAX_BUSINESS_NAMES_SHOW) ? true : false;
    }

    public function configure()
    {
        $this->setPerPageOptions([10, 25, 50, 100, 250, 500]);

        $this->setTemplate('delete', 'OxaSonataAdminBundle:CRUD:custom_field_delete.html.twig');
    }
}
