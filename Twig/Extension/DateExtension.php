<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use DateTime;
use Meniam\Bundle\CoreBundle\Traits\DateTrait;
use Twig\TwigFilter;

class DateExtension extends AbstractServiceSubscriberExtension
{
    use DateTrait;

    public function getFilters()
    {
        return [
            new TwigFilter('date_html_format', [$this, 'dateHtmlFormat']),
        ];
    }

    public function dateHtmlFormat($date, $format = 'd.m.Y H:i', $withTimeTag = false)
    {
        $locale = $this->getDateLocale();
        $oldLocal = setlocale(LC_TIME, $locale);

        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $result = date($format, $date);
        setlocale(LC_TIME, $oldLocal);

        if ($withTimeTag) {
            return sprintf('<time datetime="%s">%s</time>', date('c', $date), $result);
        } else {
            return $result;
        }
    }
}