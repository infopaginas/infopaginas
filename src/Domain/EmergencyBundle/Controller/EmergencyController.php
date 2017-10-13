<?php

namespace Domain\EmergencyBundle\Controller;

use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Domain\PageBundle\Model\PageInterface;
use Domain\SearchBundle\Util\SearchDataUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmergencyController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function emergencyAction(Request $request)
    {
        $emergencyManger = $this->getEmergencyManager();

        if (!$emergencyManger->getEmergencyFeatureEnabled()) {
            throw $this->createNotFoundException();
        }

        $pageManager = $this->get('domain_page.manager.page');
        $page = $pageManager->getPageByCode(PageInterface::CODE_EMERGENCY);

        $catalogItems = $emergencyManger->getCatalogItemsWithContent();

        $pageLinks = $page->getLinksGroupedByTypes();

        return $this->render(
            ':redesign:emergency-main.html.twig',
            [
                'page'       => $page,
                'pageLinks'  => $pageLinks,
                'seoData'    => $pageManager->getPageSeoData($page),
                'catalogItems' => $catalogItems,
            ]
        );
    }

    /**
     * @param Request $request
     * @param string  $areaSlug
     * @param string  $categorySlug
     *
     * @return Response|JsonResponse
     */
    public function catalogAction(Request $request, $areaSlug, $categorySlug)
    {
        $emergencyManger = $this->getEmergencyManager();

        if (!$emergencyManger->getEmergencyFeatureEnabled()) {
            throw $this->createNotFoundException();
        }

        $area     = $emergencyManger->getAreaBySlug($areaSlug);
        $category = $emergencyManger->getCategoryBySlug($categorySlug);
        $pageNumber = SearchDataUtil::getPageFromRequest($request);

        if ($area and $category) {
            $searchManager = $this->get('domain_search.manager.search');

            $searchParams = $searchManager->getEmergencySearchDTO($request, $area->getId(), $category->getId());
            $businesses   = $searchManager->searchEmergencyBusinessByAreaAndCategory($searchParams);

            $placeholders = [
                '[area]'      => $area->getName(),
                '[category]'  => $category->getName(),
            ];
        } else {
            $businesses = [];
            $placeholders = [];
        }

        if ($request->getMethod() == Request::METHOD_POST) {
            if ($businesses or $pageNumber == 1) {
                $html = $this->renderView(
                    ':redesign/blocks/emergency:emergency-businesses.html.twig',
                    [
                        'area'       => $area,
                        'category'   => $category,
                        'pageNumber' => $pageNumber,
                        'businesses' => $businesses,
                    ]
                );
            } else {
                $html = '';
            }

            return new JsonResponse([
                'html' => $html,
            ]);
        } else {
            $pageManager = $this->get('domain_page.manager.page');
            $page = $pageManager->getPageByCode(PageInterface::CODE_EMERGENCY_AREA_CATEGORY);

            $letters = $emergencyManger->getCatalogItemCharacterFilters($area, $category);

            $serviceFilters = $emergencyManger->getCatalogItemServiceFilters();

            return $this->render(
                ':redesign:emergency-catalog.html.twig',
                [
                    'page'       => $page,
                    'seoData'    => $pageManager->getPageSeoData($page, $placeholders),
                    'area'       => $area,
                    'category'   => $category,
                    'pageNumber' => $pageNumber,
                    'businesses' => $businesses,
                    'letters'    => $letters,
                    'serviceFilters' => $serviceFilters,
                ]
            );
        }
    }

    /**
     * @return EmergencyManager
     */
    protected function getEmergencyManager()
    {
        return $this->get('domain_emergency.manager.emergency');
    }
}
