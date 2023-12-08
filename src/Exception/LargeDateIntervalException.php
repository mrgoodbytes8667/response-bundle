<?php

namespace Bytes\ResponseBundle\Exception;

use DateInterval;
use LogicException;
use Throwable;

class LargeDateIntervalException extends LogicException
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @see https://php.net/manual/en/exception.construct.php
     *
     * @param string         $message  [optional] The Exception message to throw
     * @param int            $code     [optional] The Exception code
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining
     */
    public function __construct(private readonly ?DateInterval $interval, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getInterval(): ?DateInterval
    {
        return $this->interval;
    }
}
