<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\Entity\CSVImportFile;
use Twig_Extension;

class CSVImportFileExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            'get_business_profile_csv_mapping_html' => new \Twig_Function_Method(
                $this,
                'getBusinessProfileCSVMappingHTML',
                [
                    'needs_environment' => true,
                    'is_safe'           => [
                        'html',
                    ],
                ]
            ),
        ];
    }

    public function getBusinessProfileCSVMappingHTML(\Twig_Environment $environment)
    {
        $mappingFields = CSVImportFile::getBusinessProfileMappingFields();

        $html = $environment->render(':redesign/blocks/csvImport:mapping_fields.html.twig', [
            'mappingFields' => $mappingFields,
        ]);

        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'get_business_profile_csv_mapping_html_extension';
    }
}
