<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use \Exception;
use \DateTime;
use \DateTimeZone;

trait DateTrait
{
    public function date($time='now', DateTimeZone $timezone=null)
    {
        try {
            return new DateTime($time, $timezone);
        } catch (Exception $e) {
            return null;
        }
    }
}
