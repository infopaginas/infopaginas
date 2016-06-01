<?php

namespace Oxa\Sonata\UserBundle\Controller;

use Oxa\Sonata\AdminBundle\Controller\CRUDController as AdminCRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CRUDController extends AdminCRUDController
{
    /**
     * Extended to allow any user to show his profile info
     *
     * @param null $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function showAction($id = null)
    {
        $request = $this->getRequest();
        $id      = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        // allow user to see his profile info even he is not granted
        // it's simple alternative to profile page
        if ($id != $object->getId() && false === $this->admin->isGranted('VIEW', $object)) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException;
        }

        $this->admin->setSubject($object);

        return $this->render($this->admin->getTemplate('show'), [
                'action'   => 'show',
                'object'   => $object,
                'elements' => $this->admin->getShow(),
            ], 
            null
        );
    }
}
