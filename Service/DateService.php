<?php

namespace Meniam\Bundle\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DateService extends AbstractCoreService
{
    private $dateReplaces;

    public function __construct(ContainerInterface $container, $dateReplaces)
    {
        parent::__construct($container);
        $this->dateReplaces = $dateReplaces;
    }

    public function dateReplace(string $date, $locale = null)
    {
        if (!$locale) $locale = 'ru';

        if (isset($this->dateReplaces[$locale])) {
            $date = strtr($date, $this->dateReplaces[$locale]);
        }

        return $date;
    }
}
