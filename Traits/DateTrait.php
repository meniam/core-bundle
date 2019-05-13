<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use \DateTimeZone;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property ContainerInterface $container
 */
trait DateTrait
{
    use ServiceSystemTrait;

    public function date($time='now', DateTimeZone $timezone=null)
    {
        return $this->getDateService()->date($time, $timezone);
    }

    public function dateReplace(string $date, $locale = null)
    {
        return $this->getDateService()->dateReplace($date, $locale);
    }
}
