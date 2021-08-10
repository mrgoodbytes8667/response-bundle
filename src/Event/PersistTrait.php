<?php

namespace Bytes\ResponseBundle\Event;

/**
 *
 */
trait PersistTrait
{
    /**
     * @var bool
     */
    private $persist = true;

    /**
     * @return bool
     */
    public function getPersist(): bool
    {
        return $this->persist;
    }

    /**
     * @param bool $persist
     * @return $this
     */
    public function setPersist(bool $persist): self
    {
        $this->persist = $persist;
        return $this;
    }
}