<?php

namespace Bytes\ResponseBundle\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Trait ValidatorTrait.
 */
trait ValidatorTrait
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;

        return $this;
    }
}
