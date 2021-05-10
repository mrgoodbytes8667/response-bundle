<?php


namespace Bytes\ResponseBundle\Security;


class OAuthHandlerCollection
{
    /**
     * OAuthHandlerCollection constructor.
     * @param array $list
     */
    public function __construct(private $list = [])
    {
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     * @return $this
     */
    public function setList($list): self
    {
        $this->list = $list;
        return $this;
    }

}