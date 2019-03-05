<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Predis\Client;

/**
 * Class ConsumptionPusherSubscriber
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class ConsumptionPusherSubscriber
{
    /**
     * @var Client
     */
    protected $cacheClient;

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
     * @param Client       $cacheClient
     * @param TokenStorage $tokenStorage
     * @param Router       $router
     * @param string       $apiPattern
     * @param string       $getterUserId
     * @param string       $getterUserUsername
     */
    public function __construct(Client $cacheClient, TokenStorage $tokenStorage, Router $router, $apiPattern, $getterUserId, $getterUserUsername)
    {
        $this->cacheClient  = $cacheClient;
        $this->tokenStorage = $tokenStorage;
        $this->router       = $router;
        $this->parameters   = compact('apiPattern', 'getterUserId', 'getterUserUsername');
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $method     = $event->getRequest()->getMethod();
        $uri        = $event->getRequest()->getRequestUri();
        $routeName  = $event->getRequest()->get('_route');
        $user       = $this->tokenStorage->getToken()->getUser();

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

        $key = sprintf(
            'app~consumption~%s~%s~%s~%s~%s',
            $user->$getterUserId(),
            $user->$getterUserUsername(),
            date('Ymd'),
            $method,
            $uri
        );

        $this->cacheClient->incr($key);
    }
}
