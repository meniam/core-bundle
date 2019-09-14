<?php

namespace Meniam\Bundle\CoreBundle\Util;

use \DOMDocument;
use \DOMElement;
use \DOMLettersIterator;
use \DOMNode;
use \DOMText;
use \DOMWordsIterator;
use Ramsey\Uuid\Uuid;
use Exception;

class StringUtil
{
    public static function lcfirst($str, $encoding = 'UTF-8')
    {
        $secondChar = mb_substr($str, 1, 1, 'UTF-8');
        if ($secondChar && !self::isCharUpperCase($secondChar)) {
            return mb_strtolower(mb_substr($str, 0, 1, $encoding)) . mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        return $str;
    }

    public static function lower($str, $encoding = 'UTF-8')
    {
        return mb_strtolower($str, $encoding);
    }

    public static function upper($str, $encoding = 'UTF-8')
    {
        return mb_strtoupper($str, $encoding);
    }

    public static function ucfirst($str, $encoding = 'UTF-8')
    {
        return mb_strtoupper(mb_substr($str, 0, 1, $encoding)) . mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }

    public static function ucwords($str, $encoding = 'UTF-8')
    {
        return mb_convert_case($str, MB_CASE_TITLE, $encoding);
    }

    public static function strlen($str, $encoding = 'UTF-8')
    {
        return mb_strlen($str, $encoding);
    }

    public static function substr($str, $start, $length = null, $encoding = 'UTF-8')
    {
        return mb_substr($str, $start, $length, $encoding);
    }

    public static function getDomain($url, $withWww = true)
    {
        $result = parse_url($url, PHP_URL_HOST);
        if (!$withWww) $result = preg_replace('#^www\.#i', '', $result);
        return $result;
    }

    public static function isCharUpperCase($char)
    {
        $upper = 'ЙЦУКЕНГШЩЗХЪЁЭЖДЛОРПАВЫФЯЧСМИТЬБЮQWERTYUIOPLKJHGFDSAZXCVBNM';
        return (mb_strpos($upper, $char, 0, 'UTF-8') !== false);
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
    public static function endsWith($haystack, $needle, $caseSensitive = true)
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
    public static function contains($haystack, $needle, $caseSensitive = true)
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

    public static function truncate($value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, 'UTF-8') <= $length) {
            return $value;
        }

        if ($preserve) {
            // If breakpoint is on the last word, return the value without separator.
            if (false === ($breakpoint = mb_strpos($value, ' ', $length, 'UTF-8'))) {
                return $value;
            }
            $length = $breakpoint;
        }

        return rtrim(mb_substr($value, 0, $length, 'UTF-8')).$separator;
    }

    public static function safeTruncate($string, $limit = 150)
    {
        return static::truncate($string, $limit, true);
    }

    public static function truncateHtml($html, $lengthOrWordCount = 30, $isWords = false, $separator = '...')
    {
        return $isWords ? self::truncateHtmlWords($html, $lengthOrWordCount) : self::truncateHtmlLetters($html, $lengthOrWordCount, $separator);
    }

    public static function safeTruncateHtml($html, $lengthOrWordCount = 30, $isWords = false, $separator = '...')
    {
        return static::truncateHtml($html, $lengthOrWordCount, $isWords, $separator);
    }

    /**
     * Safely truncates HTML by a given number of words.
     * @param  string  $html     Input HTML.
     * @param  integer $limit    Limit to how many words we preserve.
     * @param  string  $ellipsis String to use as ellipsis (if any).
     * @return string            Safe truncated HTML.
     */
    private static function truncateHtmlWords($html, $limit = 0, $ellipsis = "")
    {
        if ($limit <= 0) return $html;

        $dom = self::htmlToDomDocument($html);

        // Grab the body of our DOM.
        $body = $dom->getElementsByTagName("body")->item(0);

        // Iterate over words.
        $words = new DOMWordsIterator($body);

        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($words as $null) {

            // If we have exceeded the limit, we delete the remainder of the content.
            if ($words->key() >= $limit) {

                // Grab current position.
                $currentWordPosition = $words->currentWordPosition();
                $curNode = $currentWordPosition[0];
                $offset = $currentWordPosition[1];
                $words = $currentWordPosition[2];

                $curNode->nodeValue = substr(
                    $curNode->nodeValue,
                    0,
                    $words[$offset][1] + strlen($words[$offset][0])
                );

                self::removeProceedingNodes($curNode, $body);

                if (!empty($ellipsis)) {
                    self::insertEllipsis($curNode, $ellipsis);
                }

                break;
            }

        }

        return $dom->saveHTML();
    }

    /**
     * Safely truncates HTML by a given number of letters.
     * @param  string  $html     Input HTML.
     * @param  integer $limit    Limit to how many letters we preserve.
     * @param  string  $ellipsis String to use as ellipsis (if any).
     * @return string            Safe truncated HTML.
     */
    private static function truncateHtmlLetters($html, $limit = 0, $ellipsis = "")
    {
        if ($limit <= 0) return $html;

        $dom = self::htmlToDomDocument($html);

        // Grab the body of our DOM.
        $body = $dom->getElementsByTagName("body")->item(0);

        // Iterate over letters.
        $letters = new DOMLettersIterator($body);

        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($letters as $letter) {
            // If we have exceeded the limit, we want to delete the remainder of this document.
            if ($letters->key() >= $limit) {

                $currentText = $letters->currentTextPosition();
                $currentText[0]->nodeValue = substr($currentText[0]->nodeValue, 0, $currentText[1] + 1);
                self::removeProceedingNodes($currentText[0], $body);

                if (!empty($ellipsis)) {
                    self::insertEllipsis($currentText[0], $ellipsis);
                }

                break;
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Builds a DOMDocument object from a string containing HTML.
     *
     * @param string HTML to load
     * @returns DOMDocument Returns a DOMDocument object.
     * @return DOMDocument
     */
    private static function htmlToDomDocument($html)
    {
        // Transform multibyte entities which otherwise display incorrectly.
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Internal errors enabled as HTML5 not fully supported.
        libxml_use_internal_errors(true);

        // Instantiate new DOMDocument object, and then load in UTF-8 HTML.
        $dom = new DOMDocument();
        $dom->encoding = 'UTF-8';
        $dom->loadHTML($html);

        return $dom;
    }

    /**
     * Removes all nodes after the current node.
     * @param  DOMNode|DOMElement $domNode
     * @param  DOMNode|DOMElement $topNode
     * @return void
     */
    private static function removeProceedingNodes($domNode, $topNode)
    {
        $nextNode = $domNode->nextSibling;

        if ($nextNode !== null) {
            self::removeProceedingNodes($nextNode, $topNode);
            $domNode->parentNode->removeChild($nextNode);
        } else {
            //scan upwards till we find a sibling
            $curNode = $domNode->parentNode;
            while ($curNode !== $topNode) {
                if ($curNode->nextSibling !== null) {
                    $curNode = $curNode->nextSibling;
                    self::removeProceedingNodes($curNode, $topNode);
                    $curNode->parentNode->removeChild($curNode);
                    break;
                }
                $curNode = $curNode->parentNode;
            }
        }
    }

    /**
     * Inserts an ellipsis
     * @param  DOMNode|DOMElement $domNode  Element to insert after.
     * @param  string             $ellipsis Text used to suffix our document.
     * @return void
     */
    private static function insertEllipsis($domNode, $ellipsis)
    {
        $avoid = array('a', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5'); //html tags to avoid appending the ellipsis to

        if (in_array($domNode->parentNode->nodeName, $avoid) && $domNode->parentNode->parentNode !== null) {
            // Append as text node to parent instead
            $textNode = new DOMText($ellipsis);

            if ($domNode->parentNode->parentNode->nextSibling) {
                $domNode->parentNode->parentNode->insertBefore($textNode, $domNode->parentNode->parentNode->nextSibling);
            } else {
                $domNode->parentNode->parentNode->appendChild($textNode);
            }

        } else {
            // Append to current node
            $domNode->nodeValue = rtrim($domNode->nodeValue) . $ellipsis;
        }
    }

    /**
     * @param $path
     * @param $sect
     * @return mixed|null
     */
    public static function getPathSect($path, $sect)
    {
        $trimmedPath = trim($path, '/');
        $pathArray =  explode('/', $trimmedPath);

        if (!$cnt = count($pathArray)) {
            return null;
        }

        if ($sect < 0) {
            $cnt = $cnt + $sect;
        }

        return isset($pathArray[$cnt]) ? $pathArray[$cnt] : null;
    }

    /**
     * @param $path
     * @param $sect
     * @return mixed|null
     */
    public static function removePathSect($path, $sect)
    {
        $trimmedPath = trim($path, '/');
        $pathArray =  explode('/', $trimmedPath);

        if (!$cnt = count($pathArray)) {
            return '';
        }

        if ($sect < 0) {
            $cnt = $cnt + $sect;
        }

        unset($pathArray[$cnt]);

        $result = '';
        foreach ($pathArray as $slug) {
            $result .= '/' . $slug;
        }

        return $result;
    }

    public static function uuid4()
    {
        try {
            return strval(Uuid::uuid4());
        } catch (Exception $e) {
            return '';
        }
    }
}
