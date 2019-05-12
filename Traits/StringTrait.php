<?php

namespace Meniam\Bundle\CoreBundle\Traits;

trait StringTrait
{
    public function lcfirst($str)
    {
        $secondChar = mb_substr($str, 1, 1, 'UTF-8');
        if ($secondChar && !$this->isCharUpperCase($secondChar)) {
            return mb_strtolower(mb_substr($str, 0, 1, 'UTF-8')) . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
        }
        return $str;
    }

    public function ucfirst($str)
    {
        return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8')) . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
    }

    public function ucwords($str)
    {
        return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    }

    public function isCharUpperCase($char)
    {
        $upper = 'ЙЦУКЕНГШЩЗХЪЁЭЖДЛОРПАВЫФЯЧСМИТЬБЮQWERTYUIOPLKJHGFDSAZXCVBNM';
        return (mb_strpos($upper, $char) !== false);
    }


    /**
     * Check if the $haystack string starts with the substring $needle
     *
     * @param  string          $haystack
     * @param  string|string[] $needle
     * @param bool             $caseSensitive
     * @return bool
     */
    public static function startsWith($haystack, $needle, $caseSensitive = true)
    {
        $status = false;
        $compareFunc = $caseSensitive ? 'mb_strpos' : 'mb_stripos';
        foreach ((array)$needle as $each_needle) {
            $status = $each_needle === '' || $compareFunc($haystack, $each_needle, 0, 'UTF-8') === 0;
            if ($status) {
                break;
            }
        }

        return $status;
    }

    /**
     * Check if the $haystack string ends with the substring $needle
     *
     * @param  string          $haystack
     * @param  string|string[] $needle
     * @param bool             $caseSensitive
     * @return bool
     */
    public function endsWith($haystack, $needle, $caseSensitive = true)
    {
        $status = false;
        $compareFunc = $caseSensitive ? 'mb_strrpos' : 'mb_strripos';
        foreach ((array)$needle as $each_needle) {
            $expectedPosition = mb_strlen($haystack) - mb_strlen($each_needle);
            $status = $each_needle === '' || $compareFunc($haystack, $each_needle, 0, 'UTF-8') === $expectedPosition;
            if ($status) {
                break;
            }
        }

        return $status;
    }

    /**
     * Check if the $haystack string contains the substring $needle
     *
     * @param  string          $haystack
     * @param  string|string[] $needle
     * @param  bool            $caseSensitive
     * @return bool
     */
    public function contains($haystack, $needle, $caseSensitive = true)
    {
        $status = false;
        $compareFunc = $caseSensitive ? 'mb_strpos' : 'mb_stripos';
        foreach ((array)$needle as $each_needle) {
            $status = $each_needle === '' || $compareFunc($haystack, $each_needle, 0, 'UTF-8') !== false;
            if ($status) {
                break;
            }
        }
        return $status;
    }

}
