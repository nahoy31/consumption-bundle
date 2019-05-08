<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Route;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Psr\Cache\CacheItemPoolInterface;

use Nahoy\ApiPlatform\ConsumptionBundle\Service\CacheService;

/**
 * Class ConsumptionPusherSubscriber
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class ConsumptionPusherSubscriber
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Constructor
     *
     * @param CacheService $cacheService
     * @param TokenStorage $tokenStorage
     * @param Router       $router
     * @param string       $apiPattern
     * @param string       $getterUserId
     * @param string       $getterUserUsername
     */
    public function __construct(
        CacheService $cacheService,
        TokenStorage $tokenStorage,
        Router $router,
        $apiPattern,
        $getterUserId,
        $getterUserUsername
    )
    {
        $this->cacheItemPool   = $cacheService->getCacheItemPool();
        $this->tokenStorage    = $tokenStorage;
        $this->router          = $router;
        $this->parameters      = compact('apiPattern', 'getterUserId', 'getterUserUsername');
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $method           = $event->getRequest()->getMethod();
        $uri              = $event->getRequest()->getRequestUri();
        $routeName        = $event->getRequest()->get('_route');
        $user             = $this->tokenStorage->getToken()->getUser();
        $request          = $event->getRequest();

        if (!preg_match($this->parameters['apiPattern'], $uri)) {
            return false;
        }

        /**
         * $uri "/api/aggregations?label=agencyMatchingName&page=1" must be "/api/aggregations"
         * $uri "/api/logs/987978" must be "/api/logs/{id}"
         */
        if ($event->getRequest()->getQueryString()) {
            $uri = str_replace('?' . $event->getRequest()->getQueryString(), '', $uri);
        }

        $route = $this->router->getRouteCollection()->get($routeName);
        if ($route) {
            $uri = $route->getPath();
            /**
             * $uri "/api/logs/{id}.{_format}" must be "/api/logs/{id}"
             */
            $uri = str_replace('.{_format}', '', $uri);
        }

        $getterUserId       = $this->parameters['getterUserId'];
        $getterUserUsername = $this->parameters['getterUserUsername'];
        $userId             = $propertyAccessor->getValue($user, $getterUserId);
        $username           = $propertyAccessor->getValue($user, $getterUserUsername);

        $key = sprintf(
            'app~consumption~%s~%s~%s~%s~%s',
            $userId,
            $username,
            date('Ymd'),
            $method,
            urlencode($uri)
        );

        //> save the key in the keys list
        $cacheItem = $this->cacheItemPool->getItem('app~consumption~keys~' . $userId);

        $value     = $cacheItem->get();
        $value[]   = $key;
        $value     = array_unique($value);

        $cacheItem->set($value);

        $this->cacheItemPool->save($cacheItem);
        //< save the key in the keys list

        $cacheItem = $this->cacheItemPool->getItem($key);
        $value     = $cacheItem->get() + 1;
        $cacheItem->set($value);

        $this->cacheItemPool->save($cacheItem);
    }
}
