<?php

namespace Bytes\ResponseBundle\Interfaces;

/**
 * Interface ProfileImageInterface.
 */
interface ProfileImageInterface
{
    /**
     * Return the profile image.
     */
    public function getProfileImage(int $width = null, int $height = null): ?string;
}
