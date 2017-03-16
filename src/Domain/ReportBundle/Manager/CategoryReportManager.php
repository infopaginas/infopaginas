<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/12/16
 * Time: 2:28 PM
 */

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Entity\CategoryReportCategory;
use Ivory\CKEditorBundle\Exception\Exception;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class CategoryReportManager extends BaseReportManager
{
    /**
     * @return array|\Domain\BusinessBundle\Entity\Category[]
     */
    public function getCategories()
    {
        return $this->getEntityManager()
            ->getRepository('DomainBusinessBundle:Category')
            ->findAll();
    }

    /**
     * @param array $filterParams
     * @return array
     */
    public function getCategoryVisitorsQuantitiesByFilterParams(array $filterParams = [])
    {
        $params = [];

        if (isset($filterParams['_page'])) {
            $params['page'] = $filterParams['_page'];
        }

        if (isset($filterParams['_per_page'])) {
            $params['perPage'] = $filterParams['_per_page'];
        }

        if (isset($filterParams['categoryReportCategories__date'])) {
            $params['date'] = $filterParams['categoryReportCategories__date']['value'];
        }

        return $this->getCategoryVisitorsQuantities($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getCategoryVisitorsQuantities(array $params = [])
    {
        $categoryReportElements = $this->getEntityManager()->getRepository('DomainReportBundle:CategoryReport')
            ->getCategoryVisitors($params)
        ;

        $result = [
            'results' => [],
            'categories' => [],
            'categoryVisitors' => [],
            'resultsArray' => [],
            'datePeriod' => $categoryReportElements['datePeriod']
        ];

        $locale = $this->container->getParameter('locale');

        foreach ($categoryReportElements['results'] as $key => $element) {
            $categoryName = $element['category']->getTranslation('name', $locale);

            $result['results'][$key]        = $element;
            $result['categories'][]         = $categoryName;
            $result['categoryVisitors'][]   = $element['categoryVisitors'];
            $result['resultsArray'][$key]   = [
                'categoryName' => $categoryName,
                'categoryVisitors' => $element['categoryVisitors']
            ];
        }

        return $result;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function registerBusinessVisit(BusinessProfile $businessProfile)
    {
        foreach ($businessProfile->getCategories() as $category) {
            $this->createNewCategoryReportRecord($category);
        }

        $this->getEntityManager()->flush();
    }

    protected function createNewCategoryReportRecord(Category $category)
    {
        $em = $this->getEntityManager();

        $categoryReport = $em->getRepository('DomainReportBundle:CategoryReport')
            ->findOneBy(['category' => $category]);

        if (!$categoryReport) {
            $categoryReport = new CategoryReport();
            $categoryReport->setCategory($category);
            $em->persist($categoryReport);
        }

        $categoryReportCategory = new CategoryReportCategory();
        $categoryReportCategory->setCategoryReport($categoryReport);

        $em->persist($categoryReportCategory);
    }
}
