<?php

namespace Oxa\DirxionBundle\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    //el-castillito-y-la-cocina-del-gordo

    public function indexAction()
    {
        $client = new Client();

        $request = new Request('GET', 'http://infopaginas.drxlive.com/api/businesses', [
            'Authorization' => 'Token token=x6sBKnPw7kH_7Lb0WOE4jw',
            'skip' => 2,

        ]);

        $response = $client->send($request);
        //$response->getBody()->getContents();

        //$businesses = \GuzzleHttp\json_decode($response->getBody());

        //var_dump($businesses);
        die($response->getBody());
    }
}