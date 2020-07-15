<?php

namespace Domain\BusinessBundle\Util;


class CategoryUtil
{
    /**
     * @param $categoryName
     * @return string
     */
    public static function getSingleCategoryName($categoryName) {
        $delimiter = strpos($categoryName, '/') === false ? ';' : '/';

        return trim(explode($delimiter, $categoryName)[0]);
    }
}
