<?php

namespace Application\Sonata\AdminBundle\Controller;

use Application\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Application\Sonata\AdminBundle\Model\DeleteableEntityInterface;
use Sonata\AdminBundle\Controller\CRUDController as BaseSonataCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CRUDController
 * @package Application\Sonata\AdminBundle\Controller
 */
class CRUDController extends BaseSonataCRUDController
{
    /**
     * @Security("is_granted('ROLE_PHYSICAL_DELETE_ABLE')")
     */
    public function deletePhysicalAction(Request $request)
    {
        $this->disableDeleteableListener($this->admin->getClass());
        $object = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository($this->admin->getClass())->find($request->get('id'));

        if ($this->getRestMethod() == 'DELETE') {
            if (!is_null($object)) {
                $this->deletePhysicalEntity($object);
                $this->addFlash(
                    'sonata_flash_success',
                    $this->get('translator')->trans('flash_delete_physical_action_success')
                );
            }

            return $this->saveFilterResponse();
        }

        return $this->render('ApplicationSonataAdminBundle:CRUD:physical_delete.html.twig', [
            'action' => 'physical_delete',
            'object' => $object
        ]);
    }

    /**
     * @Security("is_granted('ROLE_RESTORE_ABLE')")
     */
    public function restoreAction(Request $request)
    {
        if (!is_null($id = $request->get('id'))) {
            $this->restoreEntity($id);
            $this->addFlash('sonata_flash_success', $this->get('translator')->trans('flash_restore_action_success'));
        }

        return $this->saveFilterResponse();
    }

    public function copyAction(Request $request)
    {
        if (!is_null($id = $request->get('id'))) {
            try {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($this->cloneEntity($this->admin->getSubject()));
                $em->flush();

                $this->addFlash('sonata_flash_success', $this->get('translator')->trans('flash_batch_copy_success'));
            } catch (\Exception $e) {
                $this->addFlash('sonata_flash_error', $e->getMessage());
            }
        }

        return $this->saveFilterResponse();
    }

    /**
     * @Security("is_granted('ROLE_PHYSICAL_DELETE_ABLE')")
     */
    public function batchActionDeletePhysical(ProxyQuery $query)
    {
        $this->disableDeleteableListener($this->admin->getClass());

        foreach ($query->execute() as $entity) {
            $this->deletePhysicalEntity($entity);
        }

        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')->trans('flash_delete_physical_action_success')
        );
        return $this->saveFilterResponse();
    }

    /**
     * @Security("is_granted('ROLE_RESTORE_ABLE')")
     */
    public function batchActionRestore(ProxyQuery $query)
    {
        foreach ($query->execute() as $entity) {
            if ($entity instanceof DeleteableEntityInterface && is_null($entity->getDeletedAt())) {
                continue;
            }

            $this->restoreEntity($entity->getId());
        }

        $this->addFlash('sonata_flash_success', $this->get('translator')->trans('flash_restore_action_success'));

        return $this->saveFilterResponse();
    }

    public function batchActionCopy(ProxyQuery $query)
    {
        try {
            /* @var ModelManager $modelManager */
            $modelManager = $this->admin->getModelManager();
            $em = $modelManager->getEntityManager($this->admin->getClass());

            foreach ($query->execute() as $entity) {
                if ($entity instanceof DeleteableEntityInterface && !is_null($entity->getDeletedAt())) {
                    continue;
                }

                $em->persist($this->cloneEntity($entity));
            }

            $em->flush();

            $this->addFlash('sonata_flash_success', $this->get('translator')->trans('flash_batch_copy_success'));
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        return $this->saveFilterResponse();
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        try {
            /* @var ModelManager $modelManager */
            $modelManager = $this->admin->getModelManager();
            $em = $modelManager->getEntityManager($this->admin->getClass());

            foreach ($query->execute() as $entity) {
                if ($entity instanceof DeleteableEntityInterface && !is_null($entity->getDeletedAt())) {
                    continue;
                }

                $em->remove($entity);
            }

            $em->flush();

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        return $this->saveFilterResponse();
    }

    protected function cloneEntity(CopyableEntityInterface $entity)
    {
        $propertyAccessor = $this->get('property_accessor');
        $copyMark = $this->get('translator')->trans('copy_');

        $clone = clone $entity;
        $value = $propertyAccessor->getValue($clone, $entity->getMarkCopyPropertyName());
        $propertyAccessor->setValue($clone, $entity->getMarkCopyPropertyName(), $copyMark . $value);

        return $clone;
    }

    protected function deletePhysicalEntity($entity)
    {
        $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('ApplicationSonataUserBundle:User')
            ->deletePhysicalEntity($entity);
    }

    protected function restoreEntity($id)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        /* @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $softDeleteableFilter */
        $softDeleteableFilter = $em->getFilters()->getFilter('softdeleteable');
        $softDeleteableFilter->disableForEntity($this->admin->getClass());

        $em->getRepository('ApplicationSonataUserBundle:User')
            ->restoreEntity($this->admin->getClass(), $id);
    }

    protected function saveFilterResponse()
    {
        if (isset($_GET['filter'])) {
            return new RedirectResponse($this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ]));
        } else {
            return $this->redirect('list');
        }
    }

    protected function disableDeleteableListener($className)
    {
        /* @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $softDeleteableFilter */
        $softDeleteableFilter = $this->get('doctrine.orm.default_entity_manager')
            ->getFilters()
            ->getFilter('softdeleteable');
        $softDeleteableFilter->disableForEntity($this->admin->getClass());
    }

    /**
     * @param $entity
     * @return array
     */
    private function checkExistDependentEntity($entity)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $metadata = $em->getClassMetadata(get_class($entity));
        $existDependentField = [];
        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            if ($associationMapping['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                $methodGet = 'get' . ucfirst($associationMapping['fieldName']);
                $childs = $entity->$methodGet();
                if (count($childs)) {
                    $existDependentField[] = $this->get('translator')->trans(
                        'form.label_' . $associationMapping['fieldName'],
                        []
                    );
                }
            }
        }
        return $existDependentField;
    }

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
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        $existDependentFields = null;

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedHttpException();
        }

        if ($this->getRestMethod() == 'DELETE') {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');
            $existDependentFields = $this->checkExistDependentEntity($object);
            if (!count($existDependentFields)) {
                try {
                    $this->admin->delete($object);
                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array('result' => 'ok'));
                    }
                    $this->addFlash(
                        'sonata_flash_success',
                        $this->admin->trans(
                            'flash_delete_success',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                } catch (ModelManagerException $e) {
                    $this->logModelManagerException($e);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array('result' => 'error'));
                    }

                    $this->addFlash(
                        'sonata_flash_error',
                        $this->admin->trans(
                            'flash_delete_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
                return $this->redirectTo($object);
            } else {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans(
                        'flash_delete_error_rel',
                        array('%fields%' => implode(',', $existDependentFields))
                    )
                );
            }
        }

        return $this->render($this->admin->getTemplate('delete'), array(
            'object' => $object,
            'action' => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
            'existDependentFields' => $existDependentFields
        ));
    }

    private function logModelManagerException($e)
    {
        $context = array('exception' => $e);
        if ($e->getPrevious()) {
            $context['previous_exception_message'] = $e->getPrevious()->getMessage();
        }
        $this->getLogger()->error($e->getMessage(), $context);
    }
}
