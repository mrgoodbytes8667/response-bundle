<?php

namespace Bytes\ResponseBundle\Tests\Fixtures\Entity;

use Bytes\ResponseBundle\Entity\CreatedUpdatedEntityInterface;
use Bytes\ResponseBundle\Entity\CreatedUpdatedTrait;

class CreatedUpdatedEntity implements CreatedUpdatedEntityInterface
{
    use CreatedUpdatedTrait;

    public function __construct()
    {
        $this->initializeDates();
    }
}
