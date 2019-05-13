<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Meniam\Bundle\CoreBundle\Service\RequestService;
use Symfony\Component\Routing\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    /**
     * @var RequestService
     */
    private $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('url_save_get', [$this, 'urlSaveGetFilter'], ['is_safe' => ['all']]),
            new TwigFunction('url_domain', [$this, 'urlDomainFilter'], ['is_safe' => ['all']]),
        ];
    }

    public function urlSaveGetFilter($replace = array(), $delete = array(), $route = null, $parameters = array(), $referenceType = Router::ABSOLUTE_PATH)
    {
        return $this->requestService->urlSaveGet($replace, $delete, $route, $parameters, $referenceType);
    }

    public function urlDomainFilter($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }
}