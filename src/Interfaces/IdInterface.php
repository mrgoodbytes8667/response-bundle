<?php


namespace Bytes\ResponseBundle\Interfaces;


/**
 * Interface IdInterface
 * @package Bytes\ResponseBundle\Interfaces
 */
interface IdInterface
{
    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @param string|null $id
     * @return $this
     */
    public function setId(?string $id);
}