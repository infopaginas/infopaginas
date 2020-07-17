<?php

namespace Domain\BusinessBundle\Util;

use Domain\BusinessBundle\Entity\CSVImportFile;

class CategoryUtil
{
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
