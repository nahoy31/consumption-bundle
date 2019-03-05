<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManager;

use Predis\Client;

use Nahoy\ApiPlatform\ConsumptionBundle\Entity\Consumption;

/**
 * Class PullCommand
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class PullCommand extends Command
{
    /**
     * {@inheritdoc}
     *
     * @todo change the command name
     */
    protected $commandName = 'app:consumption:pull';

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
            ->setName($this->commandName)
            ->setDescription('Pull consumption statitics')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output    = $output;
        $this->container = $this->getApplication()->getKernel()->getContainer();
        $this->em        = $this->container->get('doctrine')->getManager();
        /** @var Client $cacheClient */
        $cacheClient     = $this->container->get('snc_redis.default');

        // get Users
        $userEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.user');
        $users = $this->em->getRepository($userEntityName)->findAll();

        // iterate on users
        /**
         * @todo uses the id getter name from parameters
         */
        foreach ($users as $user) {
            $pattern = 'app~consumption~' . $user->getId() . '~*';
            $result = $cacheClient->keys($pattern);

            if (empty($result)) {
                continue;
            }

            foreach ($result as $cacheKey) {
                $cacheValue = $cacheClient->get($cacheKey);

                $this->processMetricConsumptionCountByMethodByDay($user, $cacheKey, $cacheValue);
                $this->processMetricConsumptionTotalByDay($user, $cacheKey, $cacheValue);
                $this->processMetricConsumptionCountByMethodByMonth($user, $cacheKey, $cacheValue);
                $this->processMetricConsumptionTotalByMonth($user, $cacheKey, $cacheValue);

                $cacheClient->del($cacheKey);
            }
        }
    }

    protected function processMetricConsumptionCountByMethodByDay($user, $cacheKey, $cacheValue)
    {
        $metricName = 'consumptionCountByMethodByDay';
        $arr        = explode('~', $cacheKey);
        $date       = \DateTime::createFromFormat('Ymd', $arr[4]);

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'method'     => $arr[5],
            'uri'        => $arr[6],
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new Consumption();
            $consumption->setLastValue(0);
        }

        // create the entity
        /**
         * @todo uses the username getter name from parameters
         */
        $consumption
            ->setUser($user)
            ->setUsername($user->getUsername())
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setMethod($arr[5])
            ->setUri($arr[6])
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }

    protected function processMetricConsumptionTotalByDay($user, $cacheKey, $cacheValue)
    {
        $metricName = 'consumptionTotalByDay';
        $arr        = explode('~', $cacheKey);
        $date       = \DateTime::createFromFormat('Ymd', $arr[4]);

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new Consumption();
            $consumption->setLastValue(0);
        }

        // create the entity
        /**
         * @todo uses the username getter name from parameters
         */
        $consumption
            ->setUser($user)
            ->setUsername($user->getUsername())
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }

    protected function processMetricConsumptionCountByMethodByMonth($user, $cacheKey, $cacheValue)
    {
        $metricName = 'consumptionCountByMethodByMonth';
        $arr        = explode('~', $cacheKey);
        $date       = \DateTime::createFromFormat('Ymd', $arr[4]);
        $date       = $date->setDate($date->format('Y'), $date->format('m'), '1');

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'method'     => $arr[5],
            'uri'        => $arr[6],
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new Consumption();
            $consumption->setLastValue(0);
        }

        // create the entity
        /**
         * @todo uses the username getter name from parameters
         */
        $consumption
            ->setUser($user)
            ->setUsername($user->getUsername())
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setMethod($arr[5])
            ->setUri($arr[6])
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }

    protected function processMetricConsumptionTotalByMonth($user, $cacheKey, $cacheValue)
    {
        $metricName = 'consumptionTotalByMonth';
        $arr        = explode('~', $cacheKey);
        $date       = \DateTime::createFromFormat('Ymd', $arr[4]);
        $date       = $date->setDate($date->format('Y'), $date->format('m'), '1');

        // find the entity or create it
        $consumptionEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $consumption = $this->em->getRepository($consumptionEntityName)->findOneBy([
            'user'       => $user,
            'date'       => $date,
            'metricName' => $metricName,
        ]);

        if (empty($consumption)) {
            $consumption = new Consumption();
            $consumption->setLastValue(0);
        }

        // create the entity
        /**
         * @todo uses the username getter name from parameters
         */
        $consumption
            ->setUser($user)
            ->setUsername($user->getUsername())
            ->setMetricName($metricName)
            ->setLastValue($consumption->getLastValue() + $cacheValue)
            ->setDate($date);
        ;

        $this->em->persist($consumption);
        $this->em->flush();
    }
}
