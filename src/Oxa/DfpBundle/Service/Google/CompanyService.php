<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.09.16
 * Time: 16:21
 */

namespace Oxa\DfpBundle\Service\Google;
use Oxa\DfpBundle\Google\Api\Ads\Dfp\Lib\DfpUser;

/**
 * Class CompanyService
 * @package Oxa\DfpBundle\Service\Google
 */
class CompanyService
{
    const SERVICE_NAME = 'CompanyService';
    const API_VERSION = 'v201605';

    /**
     * @var DfpUser
     */
    protected $dfpUser;

    /**
     * ReportService constructor.
     * @param DfpUser $dfpUser
     */
    public function __construct(DfpUser $dfpUser)
    {
        $this->dfpUser = $dfpUser;
    }

    public function getAdvertiserIdByExternalIdAttr(string $externalId)
    {
        $user = $this->getDfpUser();

        $companyService = $user->GetService(self::SERVICE_NAME, self::API_VERSION);

        $statementBuilder = new \StatementBuilder();
        $statementBuilder->Where('externalId = \'' . $externalId . '\'');

        $page = $companyService->getCompaniesByStatement($statementBuilder->ToStatement());

        try {
            $companyId = $page->results[0]->id;
        } catch (\Exception $e) {
            $companyId = 0;
        }

        return $companyId;
    }

    public function getAdvertiserIdsByBusinessProfileExternalIds(array $externalIds)
    {
        if (empty($externalIds)) {
            return [];
        }

        $user = $this->getDfpUser();

        $companyService = $user->GetService(self::SERVICE_NAME, self::API_VERSION);

        array_walk($externalIds, function(&$item) {
            $item = '\'' . $item . '\'';
        });

        $statementBuilder = new \StatementBuilder();
        $statementBuilder->Where('externalId IN ( ' . implode(',', $externalIds) . ' )');

        $page = $companyService->getCompaniesByStatement($statementBuilder->ToStatement());

        $ids = [];

        foreach ($page->results as $result) {
            if (isset($result->id)) {
                $ids[$result->externalId] = $result->id;
            }
        }

        return $ids;
    }

    protected function getDfpUser()
    {
        return $this->dfpUser;
    }
}