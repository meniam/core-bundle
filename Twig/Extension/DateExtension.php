<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('date_html_format', [$this, 'dateHtmlFormat']),
        ];
    }

    public function dateHtmlFormat($date, $format = 'd.m.Y H:i', $withTimeTag = false)
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $result = date($format, $date);

        if ($withTimeTag) {
            return sprintf('<time datetime="%s">%s</time>', date('c', $date), $result);
        } else {
            return $result;
        }
    }
}