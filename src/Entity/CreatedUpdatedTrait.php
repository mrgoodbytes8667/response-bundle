<?php


namespace Bytes\ResponseBundle\Entity;


use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait CreatedUpdatedTrait
 * @package Bytes\ResponseBundle\Entity
 */
trait CreatedUpdatedTrait
{
    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface|null $createdAt
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
    public function setupDates(): self
    {
        $this->setCreatedAt(new DateTime());
        if ($this->getUpdatedAt() == null) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt ?? new DateTime();

        return $this;
    }
}