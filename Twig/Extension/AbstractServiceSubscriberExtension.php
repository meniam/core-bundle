<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension;

use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

abstract class AbstractServiceSubscriberExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public static function getSubscribedServices()
    {
        return [
            'router' => '?'.RouterInterface::class,
            'request_stack' => '?'.RequestStack::class,
            'http_kernel' => '?'.HttpKernelInterface::class,
            'session' => '?'.SessionInterface::class,
            'security.authorization_checker' => '?'.AuthorizationCheckerInterface::class,
            'templating' => '?'.EngineInterface::class,
            'twig' => '?'.Environment::class,
            'doctrine' => '?'.ManagerRegistry::class,
            'form.factory' => '?'.FormFactoryInterface::class,
            'security.token_storage' => '?'.TokenStorageInterface::class,
            'security.csrf.token_manager' => '?'.CsrfTokenManagerInterface::class,
            'parameter_bag' => '?'.ContainerBagInterface::class,
        ];
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets a container parameter by its name.
     *
     * @param string $name
     * @return mixed
     * @final
     */
    protected function getParameter(string $name)
    {
        if (!$this->container->has('parameter_bag')) {
            throw new ServiceNotFoundException('parameter_bag', null, null, [], sprintf('The "%s::getParameter()" method is missing a parameter bag to work properly. Did you forget to register your controller as a service subscriber? This can be fixed either by using autoconfiguration or by manually wiring a "parameter_bag" in the service locator passed to the controller.', \get_class($this)));
        }

        return $this->container->get('parameter_bag')->get($name);
    }

}