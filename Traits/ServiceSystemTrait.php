<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use \LogicException;
use Meniam\Bundle\CoreBundle\Service\FileStorageService;
use Meniam\Bundle\CoreBundle\Service\PageMeta;
use Meniam\Bundle\CoreBundle\Service\Pager;
use Meniam\AutotextBundle\Autotext;
use Meniam\Bundle\CoreBundle\Service\RequestService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig\Environment;

/**
 * @property ContainerInterface $container
 */
trait ServiceSystemTrait
{
    use LoggerTrait;

    /**
     * @return Request
     */
    protected function getRequest()
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->getService('request_stack');
        return $requestStack->getCurrentRequest();
    }

    /**
     * @return Router
     */
    protected function getRouter()
    {
        return $this->getService('router');
    }

    /**
     * @return mixed|object|Environment
     */
    protected function getTwig()
    {
        return $this->getService('twig');
    }

    /**
     * @return Autotext
     */
    protected function getAutotext()
    {
        return $this->getService(Autotext::class);
    }

    /**
     * @return Pager|mixed
     */
    protected function getPager()
    {
        return $this->getService(Pager::class);
    }

    /**
     * @return PageMeta|mixed
     */
    protected function getPageMeta()
    {
        return $this->getService(PageMeta::class);
    }

    /**
     * @return FileStorageService
     */
    public function getFileStorageService()
    {
        return $this->getService(FileStorageService::class);
    }

    /**
     * @return RequestService
     */
    public function getRequestService()
    {
        return $this->getService(RequestService::class);
    }

    /**
     * @param string $class
     * @return object|mixed
     */
    protected function getService(string $class)
    {
        if (!$this->container->has($class)) {
            $message = "The {$class} is not registered in your application.";
            $this->getLogger()->critical($message);
            throw new LogicException($message);
        }

        return $this->container->get($class);
    }

}
