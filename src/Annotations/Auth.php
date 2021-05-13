<?php


namespace Bytes\ResponseBundle\Annotations;

use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Class Auth
 * @package Bytes\ResponseBundle\Annotations
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Auth
{
    use ClientTrait;

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
     * @param string|null $identifier
     * @param TokenSource|string|null $tokenSource
     * @return $this
     */
    public function set(?array $scopes = [], ?string $identifier = null, TokenSource|string|null $tokenSource = null): self
    {
        $this->setScopes($scopes);
        $this->setIdentifier($identifier);
        $this->setTokenSource($tokenSource);

        return $this;
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