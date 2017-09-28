<?php

namespace Domain\EmergencyBundle\Controller;

use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Domain\PageBundle\Model\PageInterface;
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

        return $this->render(
            ':redesign:emergency-main.html.twig',
            [
                'page'       => $page,
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

        $pageManager = $this->get('domain_page.manager.page');
        $page = $pageManager->getPageByCode(PageInterface::CODE_EMERGENCY_AREA_CATEGORY);

        $area     = $emergencyManger->getAreaBySlug($areaSlug);
        $category = $emergencyManger->getCategoryBySlug($categorySlug);
        $pageNumber = $request->get('page', 1);

        if ($area and $category) {
            $businesses = $emergencyManger->getBusinessByAreaAndCategory($area, $category, $pageNumber);

            $placeholders = [
                '[area]'      => $area->getName(),
                '[category]'  => $category->getName(),
            ];
        } else {
            $businesses = [];
            $placeholders = [];
        }

        if ($request->getMethod() == Request::METHOD_POST) {
            if ($businesses) {
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
            return $this->render(
                ':redesign:emergency-catalog.html.twig',
                [
                    'page'       => $page,
                    'seoData'    => $pageManager->getPageSeoData($page, $placeholders),
                    'area'       => $area,
                    'category'   => $category,
                    'pageNumber' => $pageNumber,
                    'businesses' => $businesses,
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
