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
 */
class BaseConsumption
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("consumption")
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    protected $username;

    /**
     * @ORM\Column(name="method", type="string", length=40, nullable=true)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    protected $method;

    /**
     * @ORM\Column(name="uri", type="string", length=255, nullable=true)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    protected $uri;

    /**
     * @ORM\Column(name="metric_name", type="string", length=255, nullable=false)
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    protected $metricName;

    /**
     * @ORM\Column(name="last_value", type="integer", nullable=true)
     * @Groups("consumption")
     */
    protected $lastValue = null;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="date")
     * @Groups("consumption")
     * @ApiFilter(DateFilter::class, properties={"date"})
     */
    protected $date;

    /**
     * @Groups("consumption")
     * @ApiFilter(SearchFilter::class, strategy="exact")
     */
    protected $user;

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
