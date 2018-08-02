<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Admin\SuggestEditsAdmin;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileSuggestEdit;
use Domain\BusinessBundle\Form\Handler\AdminBusinessProfileSuggestEditsFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AdminSuggestEditsController
 *
 * @package Domain\BusinessBundle\Controller
 */
class AdminSuggestEditsController extends Controller
{
    /**
     * @param BusinessProfile $businessProfile
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function businessAction(BusinessProfile $businessProfile)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(BusinessProfileSuggestEdit::class)->getAggregatedDataByBusiness($businessProfile);
        $admin = $this->getAdmin();

        return $this->render(
            'DomainBusinessBundle:Admin/SuggestEdits:list.html.twig',
            [
                'data'                => $data,
                'base_template'       => $admin->getTemplate('layout'),
                'admin'               => $admin,
                'action'              => 'list',
                'admin_pool'          => $this->get('sonata.admin.pool'),
                'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),
                'businessProfile'     => $businessProfile,
            ]
        );
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function businessKeyAction(BusinessProfile $businessProfile)
    {
        $form = $this->get('domain_business.form.admin_business_suggest_edits');
        $formHandler = $this->getAdminBusinessProfileSuggestEditsFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->redirect($formHandler->getRedirectRoute());
            }
        } catch (\Exception $e) {
        }

        $admin = $this->getAdmin();

        return $this->render(
            '@DomainBusiness/Admin/SuggestEdits/form.html.twig',
            [
                'form'                => $form->createView(),
                'base_template'       => $admin->getTemplate('layout'),
                'admin'               => $admin,
                'action'              => 'edit',
                'admin_pool'          => $this->get('sonata.admin.pool'),
                'breadcrumbs_builder' => $this->get('sonata.admin.breadcrumbs_builder'),
                'businessProfile'     => $businessProfile,
            ]
        );
    }

    /**
     * @return SuggestEditsAdmin
     */
    private function getAdmin()
    {
        return $this->get('domain_business.admin.suggest_edits');
    }

    /**
     * @return AdminBusinessProfileSuggestEditsFormHandler
     */
    private function getAdminBusinessProfileSuggestEditsFormHandler()
    {
        return $this->get('domain_business.form.handler.admin_business_suggest_edits');
    }
}
