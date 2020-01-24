<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Doctrine\ORM\EntityManager;

use Psr\Cache\CacheItemPoolInterface;

use Nahoy\ApiPlatform\ConsumptionBundle\Entity\Consumption;

/**
 * Class PullCommand
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class PullCommand extends Command
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('nahoy:consumption:pull')
            ->setDescription('Pull consumption statitics')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output        = $output;
        $this->container     = $this->getApplication()->getKernel()->getContainer();
        $this->em            = $this->container->get('doctrine')->getManager();
        $this->cacheItemPool = $this->container->get('nahoy_api_platform_consumption.service.cache')->getCacheItemPool();
        $propertyAccessor    = PropertyAccess::createPropertyAccessor();

        // get Users
        $userEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.user');
        $users = $this->em->getRepository($userEntityName)->findAll();

        // iterate on users
        foreach ($users as $user) {
            $getter = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_id');
            $userId = $propertyAccessor->getValue($user, $getter);

            $cacheItem = $this->cacheItemPool->getItem('app~consumption~keys~' . $userId);

            $keysList  = $cacheItem->get();

            if (empty($keysList)) {
                continue;
            }

            foreach ($keysList as $cacheKey) {
                $cacheItem  = $this->cacheItemPool->getItem($cacheKey);
                $cacheValue = $cacheItem->get();

                $this->processMetricConsumptionCountByMethodByDay($user, $cacheKey, $cacheValue);
                $this->processMetricConsumptionTotalByDay($user, $cacheKey, $cacheValue);
                $this->processMetricConsumptionCountByMethodByMonth($user, $cacheKey, $cacheValue);
                $this->processMetricConsumptionTotalByMonth($user, $cacheKey, $cacheValue);

                $this->cacheItemPool->deleteItem($cacheKey);
            }
        }
    }

    protected function processMetricConsumptionCountByMethodByDay($user, $cacheKey, $cacheValue)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metricName       = 'consumptionCountByMethodByDay';
        $arr              = explode('~', $cacheKey);
        $date             = \DateTime::createFromFormat('Ymd', $arr[4]);
        $uri              = urldecode($arr[6]);

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');

        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'method'     => $arr[5],
            'uri'        => $uri,
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new $consumptionEntityName();
            $consumption->setLastValue(0);
        }

        // create the entity
        $getter   = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_username');
        $username = $propertyAccessor->getValue($user, $getter);

        $consumption
            ->setUser($user)
            ->setUsername($username)
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setMethod($arr[5])
            ->setUri($uri)
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }

    protected function processMetricConsumptionTotalByDay($user, $cacheKey, $cacheValue)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metricName       = 'consumptionTotalByDay';
        $arr              = explode('~', $cacheKey);
        $date             = \DateTime::createFromFormat('Ymd', $arr[4]);

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new $consumptionEntityName();
            $consumption->setLastValue(0);
        }

        // create the entity
        $getter   = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_username');
        $username = $propertyAccessor->getValue($user, $getter);

        $consumption
            ->setUser($user)
            ->setUsername($username)
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }

    protected function processMetricConsumptionCountByMethodByMonth($user, $cacheKey, $cacheValue)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metricName       = 'consumptionCountByMethodByMonth';
        $arr              = explode('~', $cacheKey);
        $date             = \DateTime::createFromFormat('Ymd', $arr[4]);
        $date             = $date->setDate($date->format('Y'), $date->format('m'), '1');
        $uri              = urldecode($arr[6]);

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'method'     => $arr[5],
            'uri'        => $uri,
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new $consumptionEntityName();
            $consumption->setLastValue(0);
        }

        // create the entity
        $getter   = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_username');
        $username = $propertyAccessor->getValue($user, $getter);

        $consumption
            ->setUser($user)
            ->setUsername($username)
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setMethod($arr[5])
            ->setUri($uri)
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }

    protected function processMetricConsumptionTotalByMonth($user, $cacheKey, $cacheValue)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metricName       = 'consumptionTotalByMonth';
        $arr              = explode('~', $cacheKey);
        $date             = \DateTime::createFromFormat('Ymd', $arr[4]);
        $date             = $date->setDate($date->format('Y'), $date->format('m'), '1');

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new $consumptionEntityName();
            $consumption->setLastValue(0);
        }

        // create the entity
        $getter   = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_username');
        $username = $propertyAccessor->getValue($user, $getter);

        $consumption
            ->setUser($user)
            ->setUsername($username)
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }
}
