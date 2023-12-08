<?php

namespace Bytes\ResponseBundle\Event;

trait PersistTrait
{
    /**
     * @var bool
     */
    private $persist = true;

    public function getPersist(): bool
    {
        return $this->persist;
    }

    /**
     * @return $this
     */
    public function setPersist(bool $persist): self
    {
        $this->persist = $persist;

        return $this;
    }
}
