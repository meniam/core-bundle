<?php

namespace Meniam\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Meniam\AutotextBundle\Autotext;
use Meniam\Bundle\CoreBundle\Service\DateService;
use Meniam\Bundle\CoreBundle\Service\LoggerService;
use Meniam\Bundle\CoreBundle\Service\MemcacheService;
use Meniam\Bundle\CoreBundle\Service\PageCache;
use Meniam\Bundle\CoreBundle\Service\PageMeta;
use Meniam\Bundle\CoreBundle\Service\RequestService;
use Meniam\Bundle\CoreBundle\Traits\CacheTrait;
use Meniam\Bundle\CoreBundle\Traits\ConnectionTrait;
use Meniam\Bundle\CoreBundle\Traits\PagerTrait;
use Meniam\Bundle\CoreBundle\Traits\ServiceSystemTrait;
use Meniam\Bundle\CoreBundle\Traits\StringTrait;
use Meniam\Bundle\CoreBundle\Traits\TranslatorTrait;
use Meniam\Bundle\CoreBundle\Traits\ValidatorTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractCoreController extends AbstractController
{
    use StringTrait;
    use TranslatorTrait;
    use ServiceSystemTrait;
    use CacheTrait;
    use ConnectionTrait;
    use PagerTrait;
    use ValidatorTrait;

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            RequestService::class,
            MemcacheService::class,
            LoggerInterface::class,
            LoggerService::class,
            EntityManagerInterface::class,
            PageMeta::class,
            PageCache::class,
            Autotext::class,
            ValidatorInterface::class,
            DateService::class,
        ]);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param       $data
     * @param int   $status
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function jsonResponse($data, int $status = 200, array $headers = array()): JsonResponse
    {
        return new JsonResponse(json_encode($data, JSON_UNESCAPED_UNICODE), $status, $headers, true);
    }

    protected function addFlashTrans(string $type, string $message, $params = null)
    {
        $this->addFlash($type, $this->trans($message, $params));
    }

    protected function urlSaveGet($replace = array(), $delete = array(), $route = null, $parameters = array(), $referenceType = Router::ABSOLUTE_PATH)
    {
        return $this->getRequestService()->urlSaveGet($replace, $delete, $route, $parameters, $referenceType);
    }

    /**
     * @return bool
     */
    protected function isProd()
    {
        return $this->getParameter('kernel.environment') == 'prod';
    }
}