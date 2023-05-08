<?php

namespace Bytes\ResponseBundle\Interfaces;

/**
 * Interface ProfileImageInterface
 * @package Bytes\ResponseBundle\Interfaces
 */
interface ProfileImageInterface
{
    /**
     * Return the profile image
     * @param int|null $width
     * @param int|null $height
     * @return string|null
     */
    public function getProfileImage(?int $width = null, ?int $height = null): ?string;
}
