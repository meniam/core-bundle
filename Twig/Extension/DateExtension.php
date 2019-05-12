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
            new TwigFilter('date_html_format', [$this, 'dateHtmlFormatFilter']),
            new TwigFilter('date_html_pretty', [$this, 'dateHtmlPrettyFilter']),
        ];
    }

    public function dateHtmlFormatFilter($date, $format = 'd.m.Y H:i', $withTimeTag = false)
    {
        $oldLocal = setlocale(LC_TIME, 'en', 'en_EN', 'en_EN.UTF-8');

        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $result = date($format, $date);
        $result = $this->dateReplace($result);

        setlocale(LC_TIME, $oldLocal);

        if ($withTimeTag) {
            return sprintf('<time datetime="%s">%s</time>', date('c', $date), $result);
        } else {
            return $result;
        }
    }

    public function dateHtmlPrettyFilter($date, $format = 'd.m.Y H:i', $withTimeTag = false)
    {
        $oldLocal = setlocale(LC_TIME, 'en', 'en_EN', 'en_EN.UTF-8');

        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        if ($format == 'd m Y H:i') {
            $isSameYear = (date('Y') == date('Y', strtotime($date)));

            if (!$isSameYear) {
                $result = date('d m Y, H:i', $date);
            } elseif (date('Ymd') == date('Ymd', strtotime($date))) {
                $result = date('Today, H:i', $date);
            } elseif (date('Ymd', time() - 86400) == date('Ymd', strtotime($date))) {
                $result = date('Yesterday, H:i', $date);
            } else {
                $result = date("d m, H:i", $date);
            }
        } else {
            $result = date($format, $date);
        }

        $result = $this->dateReplace($result);
        setlocale(LC_TIME, $oldLocal);

        if ($withTimeTag) {
            return sprintf('<time datetime="%s">%s</time>', date('c', $date), $result);
        } else {
            return $result;
        }
    }
}