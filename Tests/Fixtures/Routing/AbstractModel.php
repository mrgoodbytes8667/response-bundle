<?php

namespace Bytes\ResponseBundle\Tests\Fixtures\Routing;

use BadMethodCallException;
use Bytes\ResponseBundle\Routing\AbstractOAuth;
use Bytes\ResponseBundle\Routing\OAuthPromptInterface;

abstract class AbstractModel extends AbstractOAuth
{
    /**
     * @var string
     */
    protected static $endpoint = 'model';

    /**
     * @var string
     */
    protected static $promptKey = 'random';

    /**
     * @var string
     */
    protected static $baseAuthorizationCodeGrantURL = '';

    /**
     * AbstractModel constructor.
     */
    public function __construct()
    {
        $this->config = [];
    }

    /**
     * {@inheritDoc}
     */
    protected static function walkHydrateScopes(&$value, $key)
    {
        $value = (string) $value;
    }

    /**
     * Returns the $prompt argument for getAuthorizationCodeGrantURL() after normalization and validation.
     *
     * @return string|bool
     *
     * @throws BadMethodCallException
     */
    protected function normalizePrompt(bool|OAuthPromptInterface|string|null $prompt, ...$options)
    {
        return 'normalizedPrompt';
    }
}
