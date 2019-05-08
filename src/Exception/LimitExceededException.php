<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class LimitExceededException
 *
 * @author Yohann Frelicot <yfrelicot@gmail.com>
 */
class LimitExceededException extends HttpException
{
    /**
     * Constructor
     *
     * @param int         $statusCode
     * @param string      $message
     * @param string      $ip
     * @param string|null $username
     */
    public function __construct(int $statusCode, string $message, string $ip, string $username = null)
    {
        $message = sprintf($message, $username ?: $ip);

        parent::__construct($statusCode, $message);
    }
}
