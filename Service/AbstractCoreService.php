<?php

namespace Meniam\Bundle\CoreBundle\Service;

use Exception;
use Meniam\Bundle\CoreBundle\Traits\DbTrait;
use Meniam\Bundle\CoreBundle\Traits\ServiceSystemTrait;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Meniam\Bundle\CoreBundle\Filter\Rule\SuggestionSearch;
use Meniam\Bundle\CoreBundle\Filter\Rule\SuggestionSearchId;
use Meniam\Bundle\CoreBundle\Traits\CacheTrait;
use Meniam\Bundle\CoreBundle\Traits\PagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
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

abstract class AbstractCoreService implements ServiceSubscriberInterface
{
    use CacheTrait;
    use ServiceSystemTrait;
    use DbTrait;
    use PagerTrait;

    protected $container;

    public static function getSubscribedServices()
    {
        return [
            LoggerService::class,
            MemcacheService::class,
            DbService::class,
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
            'parameter_bag' => ContainerBagInterface::class,
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
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        try {
            return $this->container->get('twig')->render($view, $parameters);
        } catch (Exception $e) {
            $this->container->get(LoggerService::class)->critical("Cant render view {$view}", ['view' => $view, 'params' => $parameters]);
            return null;
        }
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