<?php

namespace Domain\ReportBundle\Google\Analytics;

/**
 * Class ClientProvider
 * @package Domain\ReportBundle\Google\Analytics
 */
class ClientProvider
{
    /**
     * @var string
     */
    protected $googleServiceAccountEmail;

    /**
     * @var string
     */
    protected $googleP12KeyPath;

    /**
     * ClientProvider constructor.
     * @param string $googleServiceAccountEmail
     * @param string $googleP12KeyPath
     */
    public function __construct(string $googleServiceAccountEmail, string $googleP12KeyPath)
    {
        $this->googleServiceAccountEmail = $googleServiceAccountEmail;
        $this->googleP12KeyPath          = $googleP12KeyPath;
    }

    /**
     * @return \Google_Client
     */
    public function getClient()
    {
        $key = file_get_contents($this->googleP12KeyPath);

        $credentials = new \Google_Auth_AssertionCredentials(
            $this->googleServiceAccountEmail,
            [\Google_Service_Analytics::ANALYTICS_READONLY],
            $key
        );

        $client = new \Google_Client();
        $client->setAssertionCredentials($credentials);

        return $client;
    }
}
