<?php


namespace Bytes\ResponseBundle\Interfaces;


/**
 * Interface UserIdInterface
 * @package Bytes\ResponseBundle\Interfaces
 */
interface UserIdInterface
{
    /**
     * id of the user
     * @return string|null
     */
    public function getUserId(): ?string;
}