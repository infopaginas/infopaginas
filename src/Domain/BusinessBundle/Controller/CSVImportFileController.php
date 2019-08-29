<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\CSVImportFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CSVFileImportController
 */
class CSVImportFileController extends Controller
{
    public function getStatusAction(int $id)
    {
        $csvImportFile = $this->getDoctrine()->getRepository(CSVImportFile::class)->find($id);

        $data = [
            'status' => $csvImportFile->isProcessed(),
        ];

        return new JsonResponse($data);
    }
}