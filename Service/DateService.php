<?php

namespace Meniam\Bundle\CoreBundle\Service;

use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DateService extends AbstractCoreService
{
    public function date($time='now', DateTimeZone $timezone=null)
    {
        try {
            return new DateTime($time, $timezone);
        } catch (Exception $e) {
            return null;
        }
    }

    public function dateReplace(string $date, $locale = null)
    {
        if (!$locale) {
            if (!$this->container->has('parameter_bag')) {
                throw new ServiceNotFoundException('parameter_bag', null, null, [], sprintf('The parameter_bag is missing a parameter bag to work properly. Did you forget to register your controller as a service subscriber? This can be fixed either by using autoconfiguration or by manually wiring a "parameter_bag" in the service locator passed to the controller.', \get_class($this)));
            }

            $locale = $this->container->get('parameter_bag')->get('locale');
        }

        $dateReplaces = $this->container->get('parameter_bag')->get('date_replace');
        if (isset($dateReplaces[$locale])) {
            $date = strtr($date, $dateReplaces[$locale]);
        }

        return $date;
    }

}
