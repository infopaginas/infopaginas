<?php

namespace Domain\ReportBundle\Controller;

use Oxa\Sonata\AdminBundle\Controller\CRUDExportController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CRUDPostponeExportController
 * @package Domain\ReportBundle\Controller
 */
class CRUDPostponeExportController extends CRUDExportController
{
    /**
     * Show action.
     *
     * @param int|string|null $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function showAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('show', $object);
        $user = $this->getUser();

        if (!($object->getUser() and $object->getUser()->getId() == $user->getId() or
            $this->admin->isGranted('ROLE_SUPRE_ADMIN'))
        ) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException;
        }

        $preResponse = $this->preShow($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        return $this->renderWithExtraParams($this->admin->getTemplate('show'), array(
            'action' => 'show',
            'object' => $object,
            'elements' => $this->admin->getShow(),
        ), null);
    }
}
