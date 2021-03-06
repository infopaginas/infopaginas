<?php

namespace Oxa\Sonata\AdminBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\ReportBundle\Entity\ExportReport;
use Domain\ReportBundle\Model\UserActionModel;
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
     * Delete action.
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
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('delete', $object);
        $adminManager = $this->get('oxa.sonata.manager.admin_manager');

        $preResponse = $this->preDelete($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        if ($this->getRestMethod() == Request::METHOD_DELETE) {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            $objectName = $this->admin->toString($object);

            try {
                $adminManager->deletePhysicalEntity($object, $this->admin);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(
                        [
                            'result' => 'ok',
                        ],
                        200,
                        []
                    );
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans(
                        $adminManager->getDeleteSuccessFlashMessage($object),
                        [
                            '%name%' => $this->escapeHtml($objectName),
                        ],
                        'SonataAdminBundle'
                    )
                );
            } catch (\Exception $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(
                        [
                            'result' => 'error',
                        ],
                        200,
                        []
                    );
                }

                $this->addFlash('sonata_flash_error', $e->getMessage());
            }

            return $this->redirectTo($object);
        }

        return $this->renderWithExtraParams(
            $this->admin->getTemplate('delete'),
            [
                'object' => $object,
                'action' => 'delete',
                'csrf_token' => $this->getCsrfToken('sonata.delete'),
            ],
            null
        );
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function restoreAction(Request $request)
    {
        try {
            $adminManager = $this->get('oxa.sonata.manager.admin_manager');
            $adminManager->restoreEntity($this->admin->getSubject(), $this->admin);
            $this->addFlash(
                'sonata_flash_success',
                $this->get('translator')->trans('flash_restore_success', [], 'SonataAdminBundle')
            );
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        return $this->saveFilterResponse();
    }

    /**
     * @param ProxyQuery $query
     *
     * @return RedirectResponse
     */
    public function batchActionRestore(ProxyQuery $query)
    {
        try {
            $adminManager = $this->get('oxa.sonata.manager.admin_manager');
            $adminManager->restoreEntities($query->execute(), $this->admin);

            $this->addFlash(
                'sonata_flash_success',
                $this->get('translator')->trans('flash_restore_success', [], 'SonataAdminBundle')
            );
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

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
            $entityArray = $query->execute();

            $adminManager = $this->get('oxa.sonata.manager.admin_manager');
            $adminManager->removeEntities($entityArray, $this->admin);

            $this->addFlash('sonata_flash_success', $adminManager->getBatchDeleteSuccessFlashMessage($entityArray));
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

    /**
     * @param Request $request
     */
    protected function preList(Request $request)
    {
        $this->admin->handleActionLog(UserActionModel::TYPE_ACTION_VIEW_LIST_PAGE);
    }

    /**
     * @param Request $request
     * @param mixed $object
     */
    protected function preShow(Request $request, $object)
    {
        $this->admin->handleActionLog(UserActionModel::TYPE_ACTION_VIEW_SHOW_PAGE, $object);
    }

    /**
     * @param Request $request
     * @param mixed $object
     */
    protected function preEdit(Request $request, $object)
    {
        if (!$request->request->get($this->admin->getUniqid(), false)) {
            $this->admin->handleActionLog(UserActionModel::TYPE_ACTION_VIEW_UPDATE_PAGE, $object);
        }
    }

    /**
     * @param Request $request
     * @param mixed $object
     */
    protected function preCreate(Request $request, $object)
    {
        $this->admin->handleActionLog(UserActionModel::TYPE_ACTION_VIEW_CREATE_PAGE, $object);
    }

    /**
     * @param Request $request
     * @param mixed $object
     */
    protected function preDelete(Request $request, $object)
    {
        $this->admin->handleActionLog(UserActionModel::TYPE_ACTION_VIEW_DELETE_PAGE, $object);
    }

    /**
     * @param Request $request
     * @param mixed $object
     */
    protected function preExport(Request $request, $object)
    {
        $this->admin->handleActionLog(UserActionModel::TYPE_ACTION_EXPORT, $object);
    }
}
