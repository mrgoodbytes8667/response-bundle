<?php


namespace Bytes\ResponseBundle\Security;


class OAuthHandlerCollection
{
    /**
     * OAuthHandlerCollection constructor.
     * @param array $list
     */
    public function __construct(private array $list = [])
    {
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * @param array $list
     * @return $this
     */
    public function setList(array $list): self
    {
        $this->list = $list;
        return $this;
    }

}