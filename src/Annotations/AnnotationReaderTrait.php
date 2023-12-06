<?php

namespace Bytes\ResponseBundle\Annotations;

use Doctrine\Common\Annotations\Reader;

trigger_deprecation('mrgoodbytes8667/response-bundle', '5.4.0', 'Annotation support will be dropped in v6.0.0.');

/**
 * @deprecated Annotation support will be dropped in v6.0.0
 */
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
