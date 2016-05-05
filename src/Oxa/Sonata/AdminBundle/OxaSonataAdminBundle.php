<?php

namespace Oxa\Sonata\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxaSonataAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}
