<?php

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
        $usefulWords = array_diff(explode(' ', $search), self::STOP_WORDS);

        return array_map(function($keyword) {
            return mb_strtolower($keyword);
        }, $usefulWords);
    }
}