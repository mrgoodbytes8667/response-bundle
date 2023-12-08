<?php

namespace Bytes\ResponseBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait UrlGeneratorTrait.
 */
trait UrlGeneratorTrait
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @return $this
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): self
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }
}
