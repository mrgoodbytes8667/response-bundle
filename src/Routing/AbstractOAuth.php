<?php

namespace Bytes\ResponseBundle\Routing;

use BadMethodCallException;
use Bytes\ResponseBundle\Handler\LocatorInterface;
use Bytes\ResponseBundle\HttpClient\Token\AbstractTokenClient;
use Bytes\ResponseBundle\Objects\Push;
use Bytes\ResponseBundle\Security\SecurityTrait;
use Bytes\ResponseBundle\Validator\ValidatorTrait;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use function Symfony\Component\String\u;

use Symfony\Component\String\UnicodeString;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Class AbstractOAuth.
 */
abstract class AbstractOAuth implements OAuthInterface, LocatorInterface
{
    use SecurityTrait;
    use UrlGeneratorTrait;
    use ValidatorTrait;

    /**
     * @var string
     */
    public const RESPONSE_TYPE = 'code';

    /**
     * @var string
     */
    protected static $endpoint;

    /**
     * @var string
     */
    protected static $promptKey;

    /**
     * @var string
     */
    protected static $baseAuthorizationCodeGrantURL;

    /**
     * Cached normalized permissions list.
     *
     * @var array
     */
    private $permissions = [];

    /**
     * Cached normalized scopes list.
     *
     * @var array
     */
    private $scopes = [];

    private array $defaultScopes = [];

    /**
     * @var array
     */
    private $defaultPermissions;

    /**
     * @var string
     */
    private $redirect;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * AbstractOAuth constructor.
     */
    public function __construct(protected ?string $clientId, protected array $config, array $options = [])
    {
        if (!isset(static::$endpoint)) {
            throw new LogicException('The static property "$endpoint" must be set by the child class.');
        }

        if (!isset(static::$promptKey)) {
            throw new LogicException('The static property "$promptKey" must be set by the child class.');
        }

        if (!isset(static::$baseAuthorizationCodeGrantURL)) {
            throw new LogicException('The static property "$baseAuthorizationCodeGrantURL" must be set by the child class.');
        }

        if (!isset($this->config[static::$endpoint])) {
            throw new LogicException('The config parameter must include the key for "$endpoint".');
        }

        $this->defaultScopes = $this->getDefaultScopes();
    }

    /**
     * @return $this
     */
    public function setDefaultScopes(array $defaultScopes): self
    {
        $this->defaultScopes = $defaultScopes;

        return $this;
    }

    abstract protected function getDefaultScopes(): array;

    /**
     * @return array
     */
    public static function hydratePermissions(array $permissions)
    {
        array_walk($permissions, ['self', 'walkHydratePermissions']);

        return $permissions;
    }

    /**
     * @return array
     */
    public static function hydrateScopes(array $scopes)
    {
        array_walk($scopes, ['self', 'walkHydrateScopes']);

        return $scopes;
    }

    abstract protected static function walkHydrateScopes(&$value, $key);

    /**
     * Get the external URL begin the OAuth token exchange process.
     */
    public function getAuthorizationUrl(string $state = null, ...$options): string
    {
        $prompt = null;
        if (isset($options['prompt'])) {
            $prompt = $options['prompt'];
            unset($options['prompt']);
        }

        return $this->getAuthorizationCodeGrantURL($this->getRedirect(), $this->defaultScopes, $state, self::RESPONSE_TYPE, $prompt, ...$options);
    }

    /**
     * @return string
     *
     * @internal
     */
    public function getAuthorizationCodeGrantURL(string $redirect, array $scopes, ?string $state, string $responseType = self::RESPONSE_TYPE, OAuthPromptInterface|string|bool $prompt = null, ...$options)
    {
        $scopes = $this->getScopes($scopes);

        $query = Push::createPush(value: $this->clientId, key: 'client_id')
            ->push(value: $redirect, key: 'redirect_uri')
            ->push(value: self::RESPONSE_TYPE, key: 'response_type')
            ->push(value: AbstractTokenClient::buildOAuthString($scopes), key: 'scope', empty: false)
            ->push(value: $state ?? $this->getState(static::$endpoint), key: 'state')
            ->push(value: static::normalizePrompt($prompt, $options), key: static::$promptKey);

        $query = $this->appendToAuthorizationCodeGrantURLQuery($query, ...$options);

        return static::getBaseAuthorizationCodeGrantURL()->append(http_build_query($this->getQueryValues($query)))->toString();
    }

    public function getScopes(array $scopes = null): array
    {
        if (!empty($this->scopes)) {
            return $this->scopes;
        }

        $this->scopes = $this->normalizeScopes(!empty($scopes) ? $scopes : $this->defaultScopes);

        return $this->scopes;
    }

    /**
     * Takes the default scopes list and adds/removes any scopes coming from the config.
     *
     * @return array
     */
    protected function normalizeScopes(array $scopes)
    {
        if (!isset($this->config[static::$endpoint]['scopes'])) {
            $this->config[static::$endpoint]['scopes'] = [];
        }

        if (array_key_exists('add', $this->config[static::$endpoint]['scopes'])) {
            $add = $this->config[static::$endpoint]['scopes']['add'];
            if (count($add) > 0) {
                array_walk($add, [static::class, 'walkHydrateScopes']);
                $scopes = array_unique(array_merge($scopes, $add));
            }
        }

        if (array_key_exists('remove', $this->config[static::$endpoint]['scopes'])) {
            $remove = $this->config[static::$endpoint]['scopes']['remove'];
            if (count($remove) > 0) {
                array_walk($remove, [static::class, 'walkHydrateScopes']);

                $scopes = Arr::where($scopes, function ($value, $key) use ($remove) {
                    return !in_array($value, $remove);
                });
            }
        }

        return $scopes;
    }

    /**
     * @return string
     */
    protected function getState(string $route)
    {
        $userId = null;
        if (!empty($this->security)) {
            $user = $this->getTokenUser();
            if (!empty($user) && method_exists($user, 'getId')) {
                $userId = $user?->getId();
            }
        }

        $userId ??= (string) new Ulid();

        return u($userId)->append($this->csrfTokenManager->getToken($userId))->toString();
    }

    /**
     * Returns the $prompt argument for getAuthorizationCodeGrantURL() after normalization and validation.
     *
     * @return string|bool
     *
     * @throws BadMethodCallException
     */
    abstract protected function normalizePrompt(OAuthPromptInterface|string|bool|null $prompt, ...$options);

    protected function appendToAuthorizationCodeGrantURLQuery(Push $query, ...$options): Push
    {
        return $query;
    }

    protected static function getBaseAuthorizationCodeGrantURL(): UnicodeString
    {
        return u(static::$baseAuthorizationCodeGrantURL)->ensureEnd('?');
    }

    /**
     * Converts the Push object to an array for http_build_query().
     */
    protected function getQueryValues(Push $query): array
    {
        return $query->toArray();
    }

    /**
     * Get the internal redirect destination URI for OAuth.
     */
    public function getRedirect(): string
    {
        $redirect = $this->redirect ?? $this->setupRedirect();
        if (is_null($redirect)) {
            throw new LogicException('ValidatorInterface cannot be null when getting redirects');
        }

        return $redirect;
    }

    /**
     * @return string
     */
    protected function setupRedirect()
    {
        if (!array_key_exists(static::$endpoint, $this->config)) {
            throw new InvalidArgumentException(sprintf('The key "%s" was not present in the configuration', (string) static::$endpoint));
        }

        if (!array_key_exists('redirects', $this->config[static::$endpoint])) {
            throw new InvalidArgumentException(sprintf('The configuration for key "%s" was not valid', (string) static::$endpoint));
        }

        switch ($this->config[static::$endpoint]['redirects']['method']) {
            case 'route_name':
                if (empty($this->urlGenerator)) {
                    throw new InvalidArgumentException('URLGeneratorInterface cannot be null when a route name is passed');
                }

                try {
                    $redirect = $this->urlGenerator->generate($this->config[static::$endpoint]['redirects']['route_name'], [], UrlGeneratorInterface::ABSOLUTE_URL);
                } catch (RouteNotFoundException $routeNotFoundException) {
                    throw new RouteNotFoundException(sprintf('In "%s", the configured route cannot be generated. %s', static::class, $routeNotFoundException->getMessage()), $routeNotFoundException->getCode(), $routeNotFoundException);
                }

                break;
            case 'url':
                $redirect = $this->config[static::$endpoint]['redirects']['url'];
                break;
            default:
                throw new InvalidArgumentException("Param 'redirect' must be one of 'route_name' or 'url'");
                break;
        }

        if (is_null($this->validator)) {
            $this->redirect = null;

            return null;
        }

        $errors = $this->validator->validate($redirect, [
            new NotBlank(),
            new Url(),
        ]);
        if (count($errors) > 0) {
            throw new ValidatorException((string) $errors);
        }

        $this->redirect = $redirect;

        return $redirect;
    }

    /**
     * @return $this
     */
    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager): self
    {
        $this->csrfTokenManager = $csrfTokenManager;

        return $this;
    }
}
