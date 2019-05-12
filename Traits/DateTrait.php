<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use \Exception;
use \DateTime;
use \DateTimeZone;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property ContainerInterface $container
 */
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

    public function getDateLocale($default = 'ru')
    {
        if (isset($this->container)) {
            return $this->container->getParameter('locale');
        } else {
            return $default;
        }
    }
}
