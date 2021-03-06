<?php

namespace Meniam\Bundle\CoreBundle\Util;

use DateTime;
use DateTimeZone;
use Exception;

class DateUtil
{
    private static $dateReplaces = [
        'ru' => [
            'Today' => 'Сегодня',
            'Yesterday' => 'Вчера',
            'January' => 'Января',
            'February' => 'Февраля',
            'March' => 'Марта',
            'April' => 'Апреля',
            'May' => 'Мая',
            'June' => 'Июня',
            'July' => 'Июля',
            'August' => 'Августа',
            'September' => 'Сентября',
            'October' => 'Остября',
            'November' => 'Ноября',
            'December' => 'Декабря',
            'Jan' => 'Янв',
            'Feb' => 'Фев',
            'Mar' => 'Мар',
            'Apr' => 'Апр',
            'Jun' => 'Июн',
            'Jul' => 'Июл',
            'Aug' => 'Авг',
            'Sep' => 'Сен',
            'Oct' => 'Ост',
            'Nov' => 'Ноя',
            'Dec' => 'Дек',
            'Monday' => 'Понедельник',
            'Tuesday' => 'Вторник',
            'Wednesday' => 'Среда',
            'Thursday' => 'Четверт',
            'Friday' => 'Пятница',
            'Saturday' => 'Суббота',
            'Sunday' => 'Воскресенье',
            'Mon' => 'Пон',
            'Tue' => 'Вто',
            'Wed' => 'Сре',
            'Thu' => 'Чет',
            'Fri' => 'Пят',
            'Sat' => 'Суб',
            'Sun' => 'Вос',
        ]
    ];

    public static function dateReplace(string $date, $locale = null)
    {
        if (!$locale) $locale = 'ru';

        if (isset(self::$dateReplaces[$locale])) {
            $date = strtr($date, self::$dateReplaces[$locale]);
        }

        return $date;
    }

    public static function dateTime($time='now', DateTimeZone $timezone=null)
    {
        try {
            return new DateTime($time, $timezone);
        } catch (Exception $e) {
            return null;
        }
    }
}
