<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Meniam\Bundle\CoreBundle\Service\RequestService;
use Meniam\Bundle\CoreBundle\Traits\ServiceSystemTrait;
use Symfony\Component\Routing\Router;
use Twig\TwigFunction;

class UrlExtension extends AbstractServiceSubscriberExtension
{
    use ServiceSystemTrait;

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            RequestService::class
        ]);
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('url_save_get', [$this, 'urlSaveGetFilter'], ['is_safe' => ['all']]),
            new TwigFunction('url_domain', [$this, 'urlDomainFilter'], ['is_safe' => ['all']]),
        );
    }

    public function urlSaveGetFilter($replace = array(), $delete = array(), $route = null, $parameters = array(), $referenceType = Router::ABSOLUTE_PATH)
    {
        return $this->getRequestService()->urlSaveGet($replace, $delete, $route, $parameters, $referenceType);
    }

    public function urlDomainFilter($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }
}