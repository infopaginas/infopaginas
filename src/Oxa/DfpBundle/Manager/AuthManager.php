<?php

namespace Oxa\DfpBundle\Manager;

use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\Common\OAuth2TokenBuilder;

/**
 * Class DfpManager
 * @package Oxa\DfpBundle\Manager
 */
class AuthManager
{
    /* path to adsapi_php.ini file */
    protected $path;

    /**
     * DfpManager constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getSession()
    {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile($this->path)
            ->build()
        ;

        $session = (new AdManagerSessionBuilder())
            ->fromFile($this->path)
            ->withOAuth2Credential($oAuth2Credential)
            ->build()
        ;

        return $session;
    }
}
