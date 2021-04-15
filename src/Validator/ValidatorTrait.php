<?php


namespace Bytes\ResponseBundle\Validator;


use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Trait ValidatorTrait
 * @package Bytes\ResponseBundle\Validator
 */
trait ValidatorTrait
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;
        return $this;
    }
}