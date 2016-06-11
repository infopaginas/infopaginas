<?php

namespace Oxa\WistiaBundle\Uploader\Model;
use Oxa\WistiaBundle\Service\WistiaAPIClient;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */
interface WistiaFileUploaderInterface
{
    public function __construct(WistiaAPIClient $wistiaAPIClient);

    public function setData(array $data);

    public function upload();
}