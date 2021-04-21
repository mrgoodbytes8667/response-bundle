<?php


namespace Bytes\ResponseBundle\UrlGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait UrlGeneratorTrait
 * @package Bytes\ResponseBundle\UrlGenerator
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