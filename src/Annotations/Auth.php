<?php


namespace Bytes\ResponseBundle\Annotations;

/**
 * Class Auth
 * @package Bytes\ResponseBundle\Annotations
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Auth
{
    /**
     * @var array
     */
    private $scopes = [];

    /**
     * Auth constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        if (isset($values['value'])) {
            $values['scopes'] = $values['value'];
            unset($values['value']);
        }
        $this->set(...$values);
    }

    /**
     * @param array|null $scopes
     */
    public function set(?array $scopes = [])
    {
        $this->setScopes($scopes);
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes ?: [];
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;
        return $this;
    }
}