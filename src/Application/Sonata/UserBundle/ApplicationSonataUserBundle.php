<?php

namespace Application\Sonata\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApplicationSonataUserBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataUserBundle';
    }
}
