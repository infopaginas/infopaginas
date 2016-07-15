<?php

namespace Oxa\Sonata\DoctrineORMAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxaSonataDoctrineORMAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataDoctrineORMAdminBundle';
    }
}
