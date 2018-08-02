<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class SuggestEditsAdmin
 *
 * @package Domain\BusinessBundle\Admin
 */
class SuggestEditsAdmin extends OxaAdmin
{
    protected $baseRoutePattern = 'suggest-edits';
    protected $baseRouteName    = 'domain_admin_business_suggest_edits';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return BusinessProfile::class;
    }

    /**
     * @param string $context
     *
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass());

        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        $query->join($query->getRootAliases()[0] . '.suggestEdits', 'se');


        return $query;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', 'identifier', [
                'route' => [
                    'name' => 'business',
                ],
            ])
            ->add('lastSuggestEditDate', 'datetime');

        $listMapper->add('_action', 'actions', [
            'actions' => [
                'custom' => [
                    'template' => 'OxaSonataAdminBundle:CRUD:list__action_custom.html.twig'
                ],
            ]
        ]);
    }

    /**
     * @return bool
     */
    public function isCustomGranted()
    {
        return true;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return string
     */
    public function getCustomUrl(BusinessProfile $businessProfile)
    {
        return $this->routeGenerator->generate(
            'domain_admin_business_suggest_edits_business',
            ['id' => $businessProfile->getId()]
        );
    }

    /**
     * @return string
     */
    public function getCustomIconCssClass()
    {
        return 'glyphicon-eye-open';
    }

    /**
     * @return string
     */
    public function getCustomTitle()
    {
        return 'action_show';
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->getConfigurationPool()->getContainer()->get('request_stack')->getCurrentRequest();
        }
        return $this->request;
    }
}
