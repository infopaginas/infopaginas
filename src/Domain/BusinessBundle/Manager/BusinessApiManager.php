<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SearchBundle\Model\Manager\SearchManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessApiManager
{
    const API_STATUS_SUCCESS = 'success';
    const API_STATUS_ERROR   = 'error';

    const API_ERROR_ACCESS_DENIED = 'access_denied';
    const API_ERROR_NOT_FOUND     = 'not_found';
    const API_ERROR_INVALID       = 'invalid_params';
    const API_ERROR_VALUE_ALREADY_EXIST = 'already_exist';

    const API_DEFAULT_LIMIT = 10;

    /** @var ContainerInterface $container */
    protected $container;

    /** @var  BusinessProfile $businessProfile */
    protected $businessProfile;

    /** @var  BusinessProfileManager $businessProfileManager */
    protected $businessProfileManager;

    /** @var  SearchManager $searchManager */
    protected $searchManager;

    /** @var  TasksManager $tasksManager */
    protected $tasksManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->businessProfileManager = $container->get('domain_business.manager.business_profile');
        $this->searchManager = $container->get('domain_search.manager.search');
        $this->tasksManager = $container->get('domain_business.manager.tasks');
    }

    protected function getSearchClosestBusinessRequiredParams()
    {
        return [
            't',
            'lat',
            'lng',
        ];
    }

    protected function addPanoramaRequiredParams()
    {
        return [
            't',
            'id',
            'panoramaId',
        ];
    }

    protected function getResponse($error)
    {
        return [
            'error'  => $error,
            'status' => $error ? self::API_STATUS_ERROR : self::API_STATUS_SUCCESS,
        ];
    }

    protected function prepareSearchClosestBusinessesParameters(array $params)
    {
        $error  = '';

        foreach ($this->getSearchClosestBusinessRequiredParams() as $param) {
            if (!array_key_exists($param, $params)) {
                return [$this->getResponse(self::API_ERROR_INVALID), $params];
            }
        }

        if ($params['t'] != $this->container->getParameter('teleportme_access_token')) {
            $error = self::API_ERROR_ACCESS_DENIED;
        }

        if (empty($params['pp'])) {
            $params['pp'] = self::API_DEFAULT_LIMIT;
        }

        if (empty($params['p'])) {
            $params['p'] = 1;
        }

        if (empty($params['q'])) {
            $params['q'] = '';
        }

        $result = $this->getResponse($error);

        return [$result, $params];
    }

    protected function prepareAddPanoramaParameters(array $params)
    {
        $error  = '';

        foreach ($this->addPanoramaRequiredParams() as $param) {
            if (!array_key_exists($param, $params)) {
                return [$this->getResponse(self::API_ERROR_INVALID), $params];
            }
        }

        if ($params['t'] != $this->container->getParameter('teleportme_access_token')) {
            $error = self::API_ERROR_ACCESS_DENIED;
        }

        if (!empty($params['id'])) {
            $businessProfile = $this->businessProfileManager->find($params['id']);

            if (!$businessProfile) {
                $error = self::API_ERROR_NOT_FOUND;
            } else {
                $this->businessProfile = $businessProfile;
            }
        }

        $result = $this->getResponse($error);

        return [$result, $params];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function searchClosestBusinesses($params)
    {
        list($result, $params) = $this->prepareSearchClosestBusinessesParameters($params);

        if (!$result['error']) {
            $result['data'] = [];

            $searchDTO    = $this->searchManager->getSearchApiDTO($params);
            $searchResult = $this->searchManager->searchClosestBusinessesApi($searchDTO);

            $result['data']  = $searchResult['data'];
            $result['total'] = $searchResult['total'];
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function addBusinessPanorama($params)
    {
        list($result, $params) = $this->prepareAddPanoramaParameters($params);

        if (!$result['error']) {
            $status = $this->tasksManager->createAddPanoramaTask($this->businessProfile, $params['panoramaId']);

            if (!$status) {
                $result = $this->getResponse(self::API_ERROR_VALUE_ALREADY_EXIST);
            }
        }

        return $result;
    }
}
