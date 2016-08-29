<?php

namespace Oxa\Sonata\AdminBundle\Route;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\DefaultRouteGenerator;

class OxaRouteGenerator extends DefaultRouteGenerator
{
    /**
     * @param AdminInterface $admin
     * @param string $name
     * @param array $parameters
     * @param bool|false $absolute
     * @return string
     */
    public function generateUrl(AdminInterface $admin, $name, array $parameters = array(), $absolute = false)
    {
        if ($name == 'create') {
            $request = $admin->getRequest();
            $admin->setRequest($request);
//            $request->setLocale('es');
        }

        return parent::generateUrl($admin, $name, $parameters, $absolute);
    }
}