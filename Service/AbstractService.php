<?php

namespace Meniam\Bundle\CoreBundle\Service;

use App\Traits\ServiceSystemTrait;
use \LogicException;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Meniam\Bundle\CoreBundle\Filter\Rule\SuggestionSearch;
use Meniam\Bundle\CoreBundle\Filter\Rule\SuggestionSearchId;
use App\Traits\CacheTrait;
use App\Traits\ConnectionTrait;
use App\Traits\PagerTrait;
use App\Traits\StringTrait;
use App\Traits\ValidatorTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractService implements ServiceSubscriberInterface
{
    use CacheTrait;
    use StringTrait;
    use ServiceSystemTrait;
    use ConnectionTrait;
    use PagerTrait;
    use ValidatorTrait;

    protected $container;

    public static function getSubscribedServices()
    {
        return [
            LoggerInterface::class,
            LoggerService::class,
            Pager::class,
            MemcacheService::class,
            EntityManagerInterface::class,
            TranslatorInterface::class,
            'validator' => '?'.ValidatorInterface::class,
            'router' => '?'.RouterInterface::class,
            'request_stack' => '?'.RequestStack::class,
            'http_kernel' => '?'.HttpKernelInterface::class,
            'session' => '?'.SessionInterface::class,
            'security.authorization_checker' => '?'.AuthorizationCheckerInterface::class,
            'templating' => '?'.EngineInterface::class,
            'twig' => '?'.Environment::class,
            'doctrine' => '?'.ManagerRegistry::class,
            'security.token_storage' => '?'.TokenStorageInterface::class,
            'parameter_bag' => '?'.ContainerBagInterface::class,
        ];
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @throws LogicException If DoctrineBundle is not available
     *
     * @final
     */
    protected function getDoctrine(): ManagerRegistry
    {
        if (!$this->container->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application. Try running "composer require symfony/orm-pack".');
        }

        return $this->container->get('doctrine');
    }

    /**
     * @return EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string      $route         The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param int $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Shortcut to return the request service.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    /**
     * Returns a rendered view.
     * @param string $view The view name
     * @param array  $parameters An array of parameters to pass to the view
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this->container->get('twig')->render($view, $parameters);
    }

    /**
     * @param array $fields
     * @param       $searchText
     * @return string
     */
    protected function prepareSuggestionsLike(array $fields, $searchText)
    {
        $result = [];
        $preparedSearchText = FilterStatic::filterValue($searchText, SuggestionSearch::class);
        foreach ($fields as $field) {
            $result[] = "lower($field) LIKE '{$preparedSearchText}'";
        }

        if ($id = FilterStatic::filterValue($searchText, SuggestionSearchId::class)) {
            $result[] = 'id = ' . $id;
        }

        return implode(' OR ', $result);
    }
}