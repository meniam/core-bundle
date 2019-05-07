<?php

namespace Meniam\Bundle\CoreBundle\Traits;

trait CacheTtlTrait
{
    /**
     * Time to live from 1 to 3 hours
     * @return int
     */
    public function getCacheTtlShort()
    {
        return mt_rand(3600, 10800);
    }

    /**
     * Time to live from 1 to 3 days
     * @return int
     */
    public function getCacheTtlMiddle()
    {
        return mt_rand(86400, 3 * 86400);
    }

    /**
     * Time to live from 7 to 21 days
     * @return int
     */
    public function getCacheTtlLong()
    {
        return mt_rand(7 * 86400, 21 * 86400);
    }

    protected function isCacheAllowed($cached = true)
    {
        return ($cached == true);
    }

}
