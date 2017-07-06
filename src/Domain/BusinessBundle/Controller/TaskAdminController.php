<?php

namespace Domain\BusinessBundle\Controller;

use Oxa\Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class TaskAdminController
 * @package Domain\BusinessBundle\Controller
 */
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

        $preResponse = $this->preList($this->getRequest());
        if ($preResponse !== null) {
            return $preResponse;
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        $totalApprovedTasksCount = $this->tasksManager->getTotalApprovedTasksCount();
        $totalRejectedTasksCount = $this->tasksManager->getTotalRejectedTasksCount();
        $totalCompleteTasksCount = $this->tasksManager->getTotalIncompleteTasksCount();

        return $this->render(
            $this->admin->getTemplate('list'),
            [
                'action'                  => 'list',
                'form'                    => $formView,
                'datagrid'                => $datagrid,
                'csrf_token'              => $this->getCsrfToken('sonata.batch'),
                'totalApprovedTasksCount' => $totalApprovedTasksCount,
                'totalRejectedTasksCount' => $totalRejectedTasksCount,
                'totalCompleteTasksCount' => $totalCompleteTasksCount,
            ],
            null
        );
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

        $preResponse = $this->preShow($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        return $this->render(
            $this->admin->getTemplate('show'),
            [
                'action'   => 'show',
                'object'   => $object,
                'elements' => $this->admin->getShow(),
            ],
            null
        );
    }
}
