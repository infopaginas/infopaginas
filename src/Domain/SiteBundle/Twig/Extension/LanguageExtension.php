<?php

namespace Domain\SiteBundle\Twig\Extension;

class LanguageExtension extends \Twig_Extension
{
    private $defaultLanguage;

    public function setDefaultLanguage(string $defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    public function getFunctions()
    {
        return [
            'get_current_language' => new \Twig_Function_Method(
                $this,
                'getCurrentLanguage',
                ['needs_environment' => true]
            ),
        ];
    }

    public function getCurrentLanguage(\Twig_Environment $environment)
    {
        if (isset($environment->getGlobals()['languages'])) {
            foreach ($environment->getGlobals()['languages'] as $key => $language) {
                if (isset($language['active']) && $language['active']) {
                    return $key;
                }
            }
        }

        return $this->defaultLanguage;
    }

    public function getName() : string
    {
        return 'language_extension';
    }
}
