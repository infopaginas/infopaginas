<?php

namespace Oxa\Sonata\AdminBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\StatusInterface;
use Pix\SortableBehaviorBundle\Controller\SortableAdminController;
use Sonata\AdminBundle\Controller\CRUDController as BaseSonataCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Customize sonata admin crud
 *
 * Class CRUDController
 * @package Oxa\Sonata\AdminBundle\Controller
 */
class CRUDController extends SortableAdminController
{
    /**
     * Delete record completely
     *
     * @Security("is_granted('ROLE_PHYSICAL_DELETE_ABLE')")
     */
    public function deletePhysicalAction(Request $request)
    {
        $objectId = ($request->get('id') != null) ? intval($request->get('id')) : $request->get('id');

        $adminManager = $this->get('oxa.sonata.manager.admin_manager');
        $object = $adminManager->getObjectByClassName($this->admin->getClass(), $objectId, true);

        if ($this->getRestMethod() == Request::METHOD_DELETE) {
            try {
                $adminManager->deletePhysicalEntity($object);
                $this->addFlash(
                    'sonata_flash_success',
                    $this->get('translator')
                        ->trans('flash_delete_physical_action_success', [], 'SonataAdminBundle')
                );
            } catch (\Exception $e) {
                $this->addFlash('sonata_flash_error', $e->getMessage());
            }

            return $this->saveFilterResponse();
        }

        return $this->render('OxaSonataAdminBundle:CRUD:physical_delete.html.twig', [
            'action' => 'physical_delete',
            'object' => $object
        ]);
    }

    /**
     * Restore softdeleted record
     *
     * @Security("is_granted('ROLE_RESTORE_ABLE')")
     */
    public function restoreAction(Request $request)
    {
        $objectId = ($request->get('id') != null) ? intval($request->get('id')) : $request->get('id');

        $adminManager = $this->get('oxa.sonata.manager.admin_manager');
        $adminManager->restoreEntityByClassName($this->admin->getClass(), $objectId, true);

        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')
                ->trans('flash_restore_action_success', [], 'SonataAdminBundle')
        );

        return $this->saveFilterResponse();
    }

    /**
     * Copy record with oll relations
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function copyAction(Request $request)
    {
        try {
            $adminManager = $this->get('oxa.sonata.manager.admin_manager');
            $adminManager->cloneEntity($this->admin->getSubject());
            $this->addFlash(
                'sonata_flash_success',
                $this->get('translator')
                    ->trans('flash_batch_copy_success', [], 'SonataAdminBundle')
            );
        } catch (\Exception $e) {
            $this->addFlash(
                'sonata_flash_error',
                $e->getMessage()
            );
        }

        return $this->saveFilterResponse();
    }

    /**
     * Delete records completely
     *
     * @Security("is_granted('ROLE_PHYSICAL_DELETE_ABLE')")
     */
    public function batchActionDeletePhysical(ProxyQuery $query)
    {
        $adminManager = $this->get('oxa.sonata.manager.admin_manager');

        try {
            $adminManager->physicalDeleteEntities($query->execute(), true);
            $this->addFlash(
                'sonata_flash_success',
                $this->get('translator')
                    ->trans('flash_delete_physical_action_success', [], 'SonataAdminBundle')
            );
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        return $this->saveFilterResponse();
    }

    /**
     * Restore softdeleted records
     *
     * @Security("is_granted('ROLE_RESTORE_ABLE')")
     */
    public function batchActionRestore(ProxyQuery $query)
    {
        $adminManager = $this->get('oxa.sonata.manager.admin_manager');
        $adminManager->restoreEntities($query->execute(), true);

        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')
                ->trans('flash_restore_action_success', [], 'SonataAdminBundle')
        );

        return $this->saveFilterResponse();
    }

    /**
     * Copy records
     *
     * @param ProxyQuery $query
     * @return RedirectResponse
     */
    public function batchActionCopy(ProxyQuery $query)
    {
        try {
            $adminManager = $this->get('oxa.sonata.manager.admin_manager');
            $adminManager->cloneEntities($query->execute());

            $this->addFlash(
                'sonata_flash_success',
                $this->get('translator')->trans('flash_batch_copy_success', [], 'SonataAdminBundle')
            );
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        return $this->saveFilterResponse();
    }

    /**
     * Delete(soft) records
     *
     * @param ProxyQueryInterface $query
     * @return RedirectResponse
     */
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        try {
            $adminManager = $this->get('oxa.sonata.manager.admin_manager');
            $adminManager->removeEntities($query->execute());

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        return $this->saveFilterResponse();
    }

    /**
     * Keep filter params if they were set
     *
     * @return RedirectResponse
     */
    protected function saveFilterResponse()
    {
        if (isset($_GET['filter'])) {
            return new RedirectResponse($this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ]));
        }

        return $this->redirect('list');
    }

    /**
     * Delete action.
     * Action is large to keep extended methos structure
     *
     * @param int|string|null $id
     *
     * @return Response|RedirectResponse
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function deleteAction($id)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        $existDependentFields = null;

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedHttpException();
        }


        if ($this->getRestMethod() == Request::METHOD_DELETE) {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');
            $adminManager = $this->get('oxa.sonata.manager.admin_manager');

            if ($object instanceOf BusinessProfile) {
                $existDependentFields = [];
            } else {
                $existDependentFields = $adminManager->checkExistDependentEntity($object);
            }

            if ($object instanceof Subscription) {
                $object->setStatus(StatusInterface::STATUS_CANCELED);
            }

            if (!count($existDependentFields)) {
                try {
                    $this->admin->delete($object);
                    if ($this->isXmlHttpRequest()) {
                        $xmlHttpResult = 'ok';
                    } else {
                        $this->addFlash(
                            'sonata_flash_success',
                            $this->get('translator')->trans(
                                'flash_delete_success',
                                array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                                'SonataAdminBundle'
                            )
                        );
                    }
                } catch (ModelManagerException $e) {
                    $this->logModelManagerException($e);

                    if ($this->isXmlHttpRequest()) {
                        $xmlHttpResult = 'error';
                    } else {
                        $this->addFlash(
                            'sonata_flash_error',
                            $this->admin->trans(
                                'flash_delete_error',
                                array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                                'SonataAdminBundle'
                            )
                        );
                    }
                }

                if (isset($xmlHttpResult)) {
                    $returnResult = $this->renderJson(array('result' => $xmlHttpResult));
                } else {
                    $returnResult = $this->redirectTo($object);
                }

                return $returnResult;
            } else {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->get('translator')->trans(
                        'flash_delete_error_rel',
                        array('%fields%' => implode(', ', $existDependentFields)),
                        'SonataAdminBundle'
                    )
                );
            }
        }

        return $this->render($this->admin->getTemplate('delete'), [
            'object' => $object,
            'action' => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
            'existDependentFields' => $existDependentFields
        ]);
    }

    /**
     * Log flush errors
     *
     * @param ModelManagerException $e
     */
    private function logModelManagerException(ModelManagerException $e)
    {
        $context = array('exception' => $e);
        if ($e->getPrevious()) {
            $context['previous_exception_message'] = $e->getPrevious()->getMessage();
        }
        $this->getLogger()->error($e->getMessage(), $context);
    }
}
