<?php

namespace Domain\BusinessBundle\Controller;

use Oxa\Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
}
