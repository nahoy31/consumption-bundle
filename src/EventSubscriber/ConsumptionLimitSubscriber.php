<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Routing\Route;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Psr\Cache\CacheItemPoolInterface;

use Nahoy\ApiPlatform\ConsumptionBundle\Service\CacheService;
use Nahoy\ApiPlatform\ConsumptionBundle\Exception\LimitExceededException;

/**
 * Class ConsumptionLimitSubscriber
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class ConsumptionLimitSubscriber
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
     * @var array
     */
    private $routesWithLimit = [];

    /**
     * @var array
     */
    private $exceptionConfig;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Constructor
     *
     * @param CacheService $cacheService
     * @param TokenStorage $tokenStorage
     * @param array        $routesWithLimit
     * @param array        $exceptionConfig
     * @param string       $apiPattern
     * @param string       $getterUserId
     * @param string       $getterUserUsername
     */
    public function __construct(
        CacheService $cacheService,
        TokenStorage $tokenStorage,
        array $routesWithLimit,
        array $exceptionConfig,
        $apiPattern,
        $getterUserId,
        $getterUserUsername
    )
    {
        $this->cacheItemPool   = $cacheService->getCacheItemPool();
        $this->tokenStorage    = $tokenStorage;
        $this->routesWithLimit = $routesWithLimit;
        $this->exceptionConfig = $exceptionConfig;
        $this->parameters      = compact('apiPattern', 'getterUserId', 'getterUserUsername');
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $user             = $this->tokenStorage->getToken()->getUser();
        $request          = $event->getRequest();
        $getterUserId     = $this->parameters['getterUserId'];

        if (!preg_match($this->parameters['apiPattern'], $event->getRequest()->getRequestUri())) {
            return false;
        }

        if ($this->isUsedRouteForLimit($request) === true) {
            // get the user limit
            $userId        = $propertyAccessor->getValue($user, $getterUserId);
            $limitCacheKey = 'app~consumption~limit~' . $userId;
            $limit         = (int) $this->cacheItemPool->getItem($limitCacheKey)->get();

            // get the user current total requests
            $totalCacheKey  = 'app~consumption~total~' . $userId;
            $totalCacheItem = $this->cacheItemPool->getItem($totalCacheKey);
            $total          = (int) $totalCacheItem->get();

            // throw an exception if the limit is reached
            if ($total >= $limit) {
                throw $this->createRateLimitExceededException($request);
            }

            // if not...
            // then increment the user current total requests
            if ($this->doIncrementTotalRequests($request) === true) {
                $total ++;
                $totalCacheItem->set($total);

                $this->cacheItemPool->save($totalCacheItem);
            }
        }
    }

    /**
     * Returns a LimitExceededException
     *
     * @param Request $request
     *
     * @return LimitExceededException
     */
    protected function createRateLimitExceededException(Request $request)
    {
        $config   = $this->exceptionConfig;
        $class    = $config['custom_exception'] ?? LimitExceededException::class;
        $username = null;

        if (null !== $token = $this->tokenStorage->getToken()) {
            if (is_object($token->getUser())) {
                $username = $token->getUsername();
            }
        }

        return new $class($config['status_code'], $config['message'], $request->getClientIp(), $username);
    }

    /**
     * Is used route for limit
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isUsedRouteForLimit(Request $request)
    {
        $uri = $request->getUri();

        // if the parameter "routes_with_limit" is empty...
        //then checking if the limit is reached is enabled in all roads
        if (empty($this->routesWithLimit)) {
            return true;
        }

        if (!empty($this->routesWithLimit)) {
            foreach ($this->routesWithLimit as $arr) {
                if (preg_match('~' . $arr['pattern'] . '~', $uri) ) {
                    return true;
                }

            }
        }
        return false;

    }

    /**
     * @return bool
     */
    protected function doIncrementTotalRequests(Request $request)
    {
        return true;
    }
}
