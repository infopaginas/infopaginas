<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\HomepageCarousel;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class BusinessProfileCRUDController
 * @package Domain\BusinessBundle\Controller
 */
class HomepageCarouselCRUDController extends CRUDController
{
    public function createAction()
    {
        $em = $this->container->get('doctrine');

        $rowCount = $em->getRepository(HomepageCarousel::class)->countRows();
        $maxRowCount = $em->getRepository(Config::class)->findOneBy(
            ['key' => ConfigInterface::HOMEPAGE_CAROUSEL_MAX_ELEMENT_COUNT]
        );

        if ($maxRowCount && $rowCount >= $maxRowCount->getValue()) {
            $this->addFlash(
                'sonata_flash_error',
                str_replace(
                    '%count%',
                    $maxRowCount->getValue(),
                    $this->trans('flash_homepage_carousel_add_error', [], 'AdminReportBundle')
                )
            );

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return parent::createAction();
    }
}
