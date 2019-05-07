<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;

class DurationToSeconds extends FilterRule
{
    public function filter($value)
    {
        $interval = null;
        try {
            $interval = new \DateInterval($value);
        } catch (\Exception $e) {
        }

        if ($interval) {
            return ($interval->y * 365 * 24 * 60 * 60) +
                ($interval->m * 30 * 24 * 60 * 60) +
                ($interval->d * 24 * 60 * 60) +
                ($interval->h * 60 * 60) +
                ($interval->i * 60) +
                $interval->s;
        }

        return 0;
    }
}