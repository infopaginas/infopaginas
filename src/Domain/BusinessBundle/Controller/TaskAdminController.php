<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilesComparator;
use Oxa\Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskAdminController extends CRUDController
{
    private $tasksManager;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        $this->configure();

        $this->tasksManager = $container->get('domain_business.manager.tasks');
    }

    /**
     * List action.
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        $totalApprovedTasksCount = $this->tasksManager->getTotalApprovedTasksCount();
        $totalRejectedTasksCount = $this->tasksManager->getTotalRejectedTasksCount();
        $totalCompleteTasksCount = $this->tasksManager->getTotalIncompleteTasksCount();

        return $this->render($this->admin->getTemplate('list'), array(
            'action'                  => 'list',
            'form'                    => $formView,
            'datagrid'                => $datagrid,
            'csrf_token'              => $this->getCsrfToken('sonata.batch'),
            'totalApprovedTasksCount' => $totalApprovedTasksCount,
            'totalRejectedTasksCount' => $totalRejectedTasksCount,
            'totalCompleteTasksCount' => $totalCompleteTasksCount,
        ), null);
    }

    /**
     * Show action.
     *
     * @param int|string|null $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function showAction($id = null)
    {
        $request = $this->getRequest();
        $id      = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        return $this->render($this->admin->getTemplate('show'), array(
            'action'   => 'show',
            'object'   => $object,
            'elements' => $this->admin->getShow(),
        ), null);
    }
}
