<?php

namespace Oxa\TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxaTranslationBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataTranslationBundle';
    }
}
