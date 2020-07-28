<?php

namespace Domain\BusinessBundle\Util;

use Domain\BusinessBundle\Entity\CSVImportFile;

class CategoryUtil
{
    public const ENCODING_UTF8       = 'UTF-8';
    public const ENCODING_ISO_8859_1 = 'ISO-8859-1';

    public static function getCategoriesNamesFromString(string $categories): array
    {
        $delimiter = ';';

        foreach (CSVImportFile::CATEGORIES_DELIMITERS as $d) {
            if (strpos($categories, $d) !== false) {
                $delimiter = $d;
            }
        }

        return array_map('trim', explode($delimiter, $categories));
    }
}
