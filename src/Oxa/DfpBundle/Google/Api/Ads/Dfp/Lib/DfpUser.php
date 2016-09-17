<?php

namespace Oxa\DfpBundle\Google\Api\Ads\Dfp\Lib;

use DfpUser as GoogleDfpUser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DfpUser
 * @package Oxa\DfpBundle\Google\Api\Ads\Dfp\Lib
 */
class DfpUser extends GoogleDfpUser
{
    /**
     * @var string
     */
    private $libVersion = '10.1.0';

    /**
     * @var string
     */
    private $libName    = 'DfpApi-PHP';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * The DfpUser constructor.
     * <p>The DfpUser class can be configured in one of two ways:
     * <ol>
     * <li>Using an authentication INI file</li>
     * <li>Using supplied credentials</li>
     * </ol></p>
     * <p>If an authentication INI file is provided and successfully loaded, those
     * values will be used unless a corresponding parameter overwrites it.
     * If the authentication INI file is not provided (e.g. it is <var>null</var>)
     * the class will attempt to load the default authentication file at the path
     * of "../auth.ini" relative to this file's directory. Any corresponding
     * parameter, which is not <var>null</var> will however, overwrite any
     * parameter loaded from the default INI.</p>
     * <p>Likewise, if a custom settings INI file is not provided, the default
     * settings INI file will be loaded from the path of "../settings.ini"
     * relative to this file's directory.</p>
     * @param string $authenticationIniPath the absolute path to the
     *     authentication INI or relative to the current directory (cwd). If
     *     <var>null</var>, the default authentication INI file will attempt to be
     *     loaded
     * @param string $applicationName the application name (required header). Will
     *     be prepended with the library name and version. Will also overwrite the
     *     applicationName entry in any INI file
     * @param string $networkCode the network code the user belongs to
     *     (optional header). Can be left <var>null</var> if the user only belongs
     *     to one network. Will overwrite the networkCode entry in any INI
     *     file
     * @param string $settingsIniPath the path to the settings INI file. If
     *     <var>null</var>, the default settings INI file will be loaded
     * @param array $oauth2Info the OAuth 2.0 information to use for requests
     * @param string $apiPropsConfigurationPath
     * @param ContainerInterface $container
     */
    public function __construct(
        $authenticationIniPath = null,
        $applicationName = null,
        $networkCode = null,
        $settingsIniPath = null,
        $oauth2Info = null,
        string $apiPropsConfigurationPath,
        ContainerInterface $container = null
    ) {
        $this->container = $container;

        $authenticationIni = $this->getAuthenticationConfigArray($authenticationIniPath);

        $applicationName = $this->GetAuthVarValue($applicationName, self::USER_AGENT_HEADER_NAME, $authenticationIni);
        $networkCode     = $this->GetAuthVarValue($networkCode, 'networkCode', $authenticationIni);
        $oauth2Info      = $this->GetAuthVarValue($oauth2Info, 'OAUTH2', $authenticationIni);

        if (isset($oauth2Info['oAuth2AdditionalScopes'])) {
            $scopes = explode(',', $oauth2Info['oAuth2AdditionalScopes']);
        }

        $scopes[] = self::OAUTH2_SCOPE;

        $this->SetOAuth2Info($oauth2Info);
        $this->SetApplicationName($applicationName);
        $this->updateClientLibraryUserAgent($applicationName);
        $this->SetNetworkCode($networkCode);
        $this->SetScopes($scopes);

        list($defaultVersion, $defaultServer) = $this->getApiPropertiesData($apiPropsConfigurationPath);

        $logsRelativePathBase = '';

        $this->loadSettings(
            $this->locateResource($settingsIniPath),
            $defaultVersion,
            $defaultServer,
            $this->getLogsDir(),
            $logsRelativePathBase
        );
    }

    /**
     * @param string $apiPropsIniFilePath
     * @return array
     */
    protected function getApiPropertiesData(string $apiPropsIniFilePath) : array
    {
        $apiProps = $this->getApiConfigurationArray($apiPropsIniFilePath);

        $versions       = explode(',', $apiProps['api.versions']);
        $defaultVersion = $versions[count($versions) - 1];
        $defaultServer  = $apiProps['api.server'];

        return [$defaultVersion, $defaultServer];
    }

    /**
     * @param string $apiPropsIniFilePath
     * @return array
     */
    protected function getApiConfigurationArray(string $apiPropsIniFilePath) : array
    {
        return parse_ini_file($this->locateResource($apiPropsIniFilePath));
    }

    /**
     * @param string $authenticationIniFilePath
     * @return array
     */
    protected function getAuthenticationConfigArray(string $authenticationIniFilePath) : array
    {
        $authenticationIniPath = realpath($this->locateResource($authenticationIniFilePath));
        return parse_ini_file($authenticationIniPath, true);
    }

    /**
     * @param string $resourcePath
     * @return string
     */
    protected function locateResource(string $resourcePath) : string
    {
        return $this->getKernel()->locateResource($resourcePath);
    }

    /**
     * @return string
     */
    protected function getLogsDir() : string
    {
        return $this->getKernel()->getLogDir();
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel() : KernelInterface
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer() : ContainerInterface
    {
        return $this->container;
    }
}