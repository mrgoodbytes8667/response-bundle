<?php

namespace Bytes\ResponseBundle\Annotations;

use Attribute;
use Bytes\ResponseBundle\Enums\TokenSource;

#[Attribute(Attribute::TARGET_METHOD)]
class Auth
{
    use ClientTrait;

    private array $scopes = [];

    public function __construct(array $values = [], ?array $scopes = [], string $identifier = null, TokenSource|string $tokenSource = null)
    {
        if (isset($values['value'])) {
            $values['scopes'] = $values['value'];
            unset($values['value']);
        }

        if (!is_null($scopes)) {
            $values['scopes'] = $scopes;
        }

        if (!is_null($identifier)) {
            $values['identifier'] = $identifier;
        }

        if (!is_null($tokenSource)) {
            $values['tokenSource'] = $tokenSource;
        }

        $this->set(...$values);
    }

    /**
     * @return $this
     */
    public function set(?array $scopes = [], string $identifier = null, TokenSource|string $tokenSource = null): self
    {
        $this->setScopes($scopes);
        $this->setIdentifier($identifier);
        $this->setTokenSource($tokenSource);

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes ?: [];
    }

    /**
     * @return $this
     */
    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }
}
