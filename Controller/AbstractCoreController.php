<?php

namespace Meniam\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Meniam\AutotextBundle\Autotext;
use Meniam\Bundle\CoreBundle\Service\LoggerService;
use Meniam\Bundle\CoreBundle\Service\MemcacheService;
use Meniam\Bundle\CoreBundle\Service\PageCache;
use Meniam\Bundle\CoreBundle\Service\PageMeta;
use Meniam\Bundle\CoreBundle\Service\RequestService;
use Meniam\Bundle\CoreBundle\Traits\CacheTrait;
use Meniam\Bundle\CoreBundle\Traits\DbTrait;
use Meniam\Bundle\CoreBundle\Traits\LoggerTrait;
use Meniam\Bundle\CoreBundle\Traits\PagerTrait;
use Meniam\Bundle\CoreBundle\Traits\ServiceSystemTrait;
use Meniam\Bundle\CoreBundle\Traits\TranslatorTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractCoreController extends AbstractController
{
    use TranslatorTrait;
    use ServiceSystemTrait;
    use CacheTrait;
    use LoggerTrait;
    use DbTrait;
    use PagerTrait;

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

    /**
     * @param string $type
     * @param string $message
     * @param null   $params
     */
    protected function addFlashTrans(string $type, string $message, $params = null)
    {
        $this->addFlash($type, $this->trans($message, $params));
    }

    /**
     * @param array $replace
     * @param array $delete
     * @param null  $route
     * @param array $parameters
     * @param int   $referenceType
     * @return mixed|string|null
     */
    protected function urlSaveGet($replace = array(), $delete = array(), $route = null, $parameters = array(), $referenceType = Router::ABSOLUTE_PATH)
    {
        return $this->getRequestService()->urlSaveGet($replace, $delete, $route, $parameters, $referenceType);
    }

    /**
     * CreateForm And HandleRequest
     *
     * @param string $type
     * @param null   $data
     * @param array  $options
     * @return FormInterface
     */
    protected function handleForm(string $type, $data = null, array $options = []): FormInterface
    {
        $form = $this->createForm($type, $data, $options);
        $form->handleRequest($this->getRequest());
        return $form;
    }

    /**
     * @return bool
     */
    protected function isProd()
    {
        return $this->getParameter('kernel.environment') == 'prod';
    }
}