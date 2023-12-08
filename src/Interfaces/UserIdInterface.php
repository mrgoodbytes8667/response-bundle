<?php

namespace Bytes\ResponseBundle\Interfaces;

/**
 * Interface UserIdInterface.
 */
interface UserIdInterface
{
    /**
     * id of the user.
     */
    public function getUserId(): ?string;
}
