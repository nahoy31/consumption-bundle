<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * Class BaseConsumption
 *
 * @ORM\MappedSuperclass()
 * @ORM\Entity()
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     attributes={
 *         "normalization_context"={"groups"={"consumption"}},
 *         "denormalization_context"={"groups"={"consumption"}}
 *     }
 * )
 */
class BaseConsumption
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("consumption")
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $username;

    /**
     * @ORM\Column(name="method", type="string", length=40, nullable=true)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $method;

    /**
     * @ORM\Column(name="uri", type="string", length=255, nullable=true)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $uri;

    /**
     * @ORM\Column(name="metric_name", type="string", length=255, nullable=false)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $metricName;

    /**
     * @ORM\Column(name="last_value", type="integer", nullable=true)
     * @Groups("consumption")
     */
    private $lastValue = null;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="date")
     * @Groups("consumption")
     * @ApiFilter(DateFilter::class, properties={"date"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return BaseConsumption
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return BaseConsumption
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set uri
     *
     * @param string $uri
     *
     * @return BaseConsumption
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set metricName
     *
     * @param string $metricName
     *
     * @return BaseConsumption
     */
    public function setMetricName($metricName)
    {
        $this->metricName = $metricName;

        return $this;
    }

    /**
     * Get metricName
     *
     * @return string
     */
    public function getMetricName()
    {
        return $this->metricName;
    }

    /**
     * Set lastValue
     *
     * @param int $lastValue
     *
     * @return BaseConsumption
     */
    public function setLastValue($lastValue)
    {
        $this->lastValue = $lastValue;

        return $this;
    }

    /**
     * Get lastValue
     *
     * @return int
     */
    public function getLastValue()
    {
        return $this->lastValue;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return BaseConsumption
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * Set user
     *
     * @param mixed $user
     *
     * @return BaseConsumption
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}
