<?php


namespace Bytes\ResponseBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait UrlGeneratorTrait
 * @package Bytes\ResponseBundle\Routing
 */
trait UrlGeneratorTrait
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @return $this
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): self
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
    }
}