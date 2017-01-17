<?php

namespace Oxa\Sonata\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxaSonataMediaBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataMediaBundle';
    }
}
