<?php

namespace Bytes\ResponseBundle\Entity;

use DateTimeInterface;

/**
 * @see CreatedUpdatedTrait
 */
interface CreatedUpdatedEntityInterface
{
    /**
     * @return $this
     */
    public function setCreatedAt(?DateTimeInterface $createdAt = null);

    /**
     * @return $this
     */
    public function initializeDates();

    public function getUpdatedAt(): ?DateTimeInterface;

    /**
     * @return $this
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt = null);
}
