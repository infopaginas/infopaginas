<?php

/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 17.08.16
 * Time: 20:42
 */
class GoogleAnalytics
{
    public function __construct()
    {
    }

    public function getData()
    {
        $client = new Google_Client();
        $client->setApplicationName('infopaginas');
        $client->setDeveloperKey('AIzaSyAMcfe0YcaaGbRE9x7O_SQL5znb6EznO0s');

        $service = new Google_Service_Analytics($client);
        //$service->data_ga->get()

        return [];
    }
}

$ga = new GoogleAnalytics();
var_dump($ga->getData());