<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Doctrine\ORM\EntityManager;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CreateQuotaCommand
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class CreateLimitsCommand extends Command
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $cacheItemPool;

    /**
     * @var ContainerInterface
     */
    protected $container;

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
            ->setName('nahoy:consumption:create-limits')
            ->setDescription('Create API limits for all users')
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

        if (false === $this->container->getParameter('nahoy_api_platform_consumption.enabled_limit')) {
            throw new \Exception(sprintf(
                'Set the parameter `%s` to `true` to enable this feature',
                'nahoy_api_platform_consumption.enabled_limit'
            ));
        }

        // get all users
        $userEntityName = $this->container->getParameter('nahoy_api_platform_consumption.class.user');
        $users          = $this->em->getRepository($userEntityName)->findAll();

        // iterate on users
        foreach ($users as $user) {
            // get the user limit
            $getter = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_limit');
            $value = $propertyAccessor->getValue($user, $getter);

            $getter = $this->container->getParameter('nahoy_api_platform_consumption.getter.user_id');
            $userId = $propertyAccessor->getValue($user, $getter);

            $cacheKey = 'app~consumption~limit~' . $userId;

            $cacheItem = $this->cacheItemPool->getItem($cacheKey);
            $cacheItem->set($value);

            $this->cacheItemPool->save($cacheItem);
        }
    }
}
