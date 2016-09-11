<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 06.09.16
 * Time: 11:41
 */

namespace Domain\ReportBundle\Service;

/**
 * Class StemmerService
 * @package Domain\ReportBundle\Service
 */
class StemmerService
{
    const STOP_WORDS = ["the", "and", "an", "of", "for", "to", "in", "a", "by", ""];

    /**
     * @param string $search
     * @return array
     */
    public function getWordsArrayFromString(string $search) : array
    {
        $normalizedSearchString = preg_replace('/[^ \w]+/', '', $search);

        $usefulWords = array_diff(explode(' ', $normalizedSearchString), self::STOP_WORDS);

        return array_map(function($keyword) {
            return ucfirst($keyword);
        }, $usefulWords);
    }
}