<?php

namespace Oxa\Sonata\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxaSonataUserBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataUserBundle';
    }
}
