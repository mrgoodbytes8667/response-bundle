<?php


namespace Bytes\ResponseBundle\Annotations;


use Doctrine\Common\Annotations\Reader;

/**
 * Trait AnnotationReaderTrait
 * @package Bytes\ResponseBundle\Annotations
 */
trait AnnotationReaderTrait
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     * @return $this
     */
    public function setReader(Reader $reader): self
    {
        $this->reader = $reader;
        return $this;
    }
}