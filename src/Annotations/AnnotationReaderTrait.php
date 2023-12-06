<?php

namespace Bytes\ResponseBundle\Annotations;

use Doctrine\Common\Annotations\Reader;

trait AnnotationReaderTrait
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @return $this
     */
    public function setReader(Reader $reader): self
    {
        $this->reader = $reader;

        return $this;
    }
}
