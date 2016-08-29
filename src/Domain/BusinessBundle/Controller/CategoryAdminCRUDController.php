<?php
/**
 * Created by PhpStorm.
 * User: sakovas
 * Date: 8/29/2016
 * Time: 1:32 PM
 */

namespace Domain\BusinessBundle\Controller;


use Oxa\Sonata\AdminBundle\Controller\CRUDController;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\HttpFoundation\Request;

class CategoryAdminCRUDController extends CRUDController
{
    public function deletePhysicalAction(Request $request)
    {
        $objectId = ($request->get('id') != null) ? intval($request->get('id')) : $request->get('id');

        $adminManager = $this->get('oxa.sonata.manager.admin_manager');
        $object = $adminManager->getObjectByClassName($this->admin->getClass(), $objectId, true);
        $existDependentFields = null;

        if ($this->getRestMethod() == Request::METHOD_DELETE) {
            $existDependentFields = $adminManager->checkExistDependentEntity($object);

            if (!count($existDependentFields)) {
                $adminManager->deletePhysicalEntity($object);
                $this->addFlash(
                    'sonata_flash_success',
                    $this->get('translator')
                        ->trans('flash_delete_physical_action_success', [], 'SonataAdminBundle')
                );

                return $this->saveFilterResponse();
            } else {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->get('translator')->trans(
                        'flash_delete_error_rel',
                        array('%fields%' => implode(',', $existDependentFields)),
                        'SonataAdminBundle'
                    )
                );
            }
        }

        return $this->render('OxaSonataAdminBundle:CRUD:physical_delete.html.twig', [
            'action' => 'physical_delete',
            'object' => $object,
            'existDependentFields' => $existDependentFields,
        ]);
    }
}