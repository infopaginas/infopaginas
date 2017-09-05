<?php

namespace Application\Sonata\ClassificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApplicationSonataClassificationBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'SonataClassificationBundle';
    }
}
