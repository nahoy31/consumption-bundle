<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Doctrine;

use DDFactory\Bundle\EmailBundle\Entity\BaseAgencyEmailService;
use DDFactory\Bundle\EmailBundle\Entity\BaseSiteEmailService;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadClassMetadataListener
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class LoadClassMetadataListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $consumption;

    /**
     * @var string
     */
    protected $user;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container   = $container;
        $this->consumption = $container->getParameter('nahoy_api_platform_consumption.class.consumption');
        $this->user        = $container->getParameter('nahoy_api_platform_consumption.class.user');
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        $class = new \ReflectionClass($metadata->getName());

        if (
            $this->consumption === $class->getName() &&
            !$metadata->isMappedSuperclass
        ) {
            $metadata->mapManyToOne([
                'targetEntity'  => $this->user,
                'fieldName'     => 'user',
                'joinColumns' => [[
                    'name'                 => 'user_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => true,
                ]]
            ]);
        }
    }
}
