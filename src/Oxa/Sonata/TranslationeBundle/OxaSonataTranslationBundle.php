<?php

namespace Oxa\Sonata\TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxaSonataTranslationBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataTranslationBundle';
    }
}
