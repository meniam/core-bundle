<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;

class Stopwords extends FilterRule
{
    private static $stopwords;

    public function filter($value)
    {
        $stopWords = $this->getStopwords();
        return preg_replace($stopWords, '', $value);
    }

    private function getStopwords()
    {
        if (!self::$stopwords) {
            $words = array_map('trim', file(__DIR__.'/stopwords.txt'));
            self::$stopwords = '#\b(' . implode('|', $words) . ')\b#usi';
        }

        return self::$stopwords;
    }
}

