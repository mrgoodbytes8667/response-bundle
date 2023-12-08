<?php

namespace Bytes\ResponseBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait CreatedUpdatedTrait.
 */
trait CreatedUpdatedTrait
{
    #[ORM\Column(type: 'datetime')]
    protected ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    protected ?DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return $this
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return $this
     */
    public function initializeDates(): self
    {
        $dateTime = new DateTime();
        $this->setCreatedAt($dateTime);
        if (null == $this->getUpdatedAt()) {
            $this->setUpdatedAt($dateTime);
        }

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt ?? new DateTime();

        return $this;
    }
}
