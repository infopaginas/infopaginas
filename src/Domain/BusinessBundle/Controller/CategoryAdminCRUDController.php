<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\Category;
use Oxa\Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class CategoryAdminCRUDController extends CRUDController
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

            $existDependentFields = $adminManager->checkExistDependentEntity($object);

            $categoryAttachedToProfiles = ($this->admin->getClass() == Category::class)
                && (count($object->getBusinessProfiles()) > 0);

            if ($categoryAttachedToProfiles) {
                $existDependentFields[] = 'Business Profiles';
            }

            if (!count($existDependentFields) && !$categoryAttachedToProfiles) {
                $objectName = $this->admin->toString($object);

                try {
                    $adminManager->deletePhysicalEntity($object);

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
                            'flash_delete_success',
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

            return $this->redirectTo($object);
        }

        return $this->render(
            $this->admin->getTemplate('delete'),
            [
                'object' => $object,
                'action' => 'delete',
                'csrf_token' => $this->getCsrfToken('sonata.delete'),
            ],
            null
        );
    }
}
