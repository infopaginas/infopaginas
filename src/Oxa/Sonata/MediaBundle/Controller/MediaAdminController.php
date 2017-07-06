<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/23/16
 * Time: 7:06 PM
 */

namespace Oxa\Sonata\MediaBundle\Controller;

use Oxa\Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class MediaAdminController extends CRUDController
{
    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $mediaContext = $this->get('request')->get('context', $this->get('sonata.media.pool')->getDefaultContext());

        $parameters = $this->admin->getPersistentParameters();
        $providers  = $this->get('sonata.media.pool')->getProvidersByContext($mediaContext);

        if (!$parameters['provider']) {
            return $this->render(
                'SonataMediaBundle:MediaAdmin:select_provider.html.twig',
                array(
                    'providers'     => $providers,
                    'base_template' => $this->getBaseTemplate(),
                    'admin'         => $this->admin,
                    'action'        => 'create',
                )
            );
        }

        return parent::createAction();
    }

    /**
     * @param string                                          $view
     * @param array                                           $parameters
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $parameters['media_pool']            = $this->container->get('sonata.media.pool');
        $parameters['persistent_parameters'] = $this->admin->getPersistentParameters();

        return parent::render($view, $parameters);
    }

    /**
     * return the Response object associated to the list action.
     *
     * @return Response
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
        if ($this->admin->getPersistentParameter('context')) {
            $datagrid->setValue('context', null, $this->admin->getPersistentParameter('context'));
        }

        if ($this->admin->getPersistentParameter('provider')) {
            $datagrid->setValue('providerName', null, $this->admin->getPersistentParameter('provider'));
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render(
            $this->admin->getTemplate('list'),
            array(
                'action'     => 'list',
                'form'       => $formView,
                'datagrid'   => $datagrid,
                'csrf_token' => $this->getCsrfToken('sonata.batch'),
            )
        );
    }
}
