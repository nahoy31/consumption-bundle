<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Service;

use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class CacheService
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * constructor
     *
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * Get cacheItemPool
     *
     * @return CacheItemPoolInterface
     */
    public function getCacheItemPool()
    {
        return $this->cacheItemPool;
    }
}
