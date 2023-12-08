<?php

namespace Bytes\ResponseBundle\Interfaces;

/**
 * Interface IdInterface.
 */
interface IdInterface
{
    public function getId(): ?string;

    /**
     * @return $this
     */
    public function setId(?string $id);
}
