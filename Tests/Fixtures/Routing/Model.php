<?php


namespace Bytes\ResponseBundle\Tests\Fixtures\Routing;


use BadMethodCallException;
use Bytes\Common\Faker\Providers\MiscProvider;
use Bytes\ResponseBundle\Routing\OAuthPromptInterface;
use Faker\Generator as FakerGenerator;
use Faker\Provider\Address;
use Faker\Provider\Barcode;
use Faker\Provider\Biased;
use Faker\Provider\Color;
use Faker\Provider\Company;
use Faker\Provider\DateTime;
use Faker\Provider\File;
use Faker\Provider\HtmlLorem;
use Faker\Provider\Image;
use Faker\Provider\Internet;
use Faker\Provider\Lorem;
use Faker\Provider\Medical;
use Faker\Provider\Miscellaneous;
use Faker\Provider\Payment;
use Faker\Provider\Person;
use Faker\Provider\PhoneNumber;
use Faker\Provider\Text;
use Faker\Provider\UserAgent;
use Faker\Provider\Uuid;
use Illuminate\Support\Arr;

class Model extends \Bytes\ResponseBundle\Routing\AbstractOAuth
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
     * @var FakerGenerator|MiscProvider|Address|Barcode|Biased|Color|Company|DateTime|File|HtmlLorem|Image|Internet|Lorem|Medical|Miscellaneous|Payment|Person|PhoneNumber|Text|UserAgent|Uuid
     */
    private $faker;

    /**
     * @inheritDoc
     */
    protected static function walkHydrateScopes(&$value, $key)
    {
        $value = (string)$value;
    }

    /**
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return $this->defaultScopes;
    }

    /**
     * Returns the $prompt argument for getAuthorizationCodeGrantURL() after normalization and validation
     * @param OAuthPromptInterface|string|bool|null $prompt
     * @param mixed ...$options
     * @return string|bool
     *
     * @throws BadMethodCallException
     */
    protected function normalizePrompt(bool|OAuthPromptInterface|string|null $prompt, ...$options)
    {
        return 'normalizedPrompt';
    }

    /**
     * AbstractOAuth constructor.
     * @param string|null $clientId
     * @param array $config
     * @param array $options
     */
    public function __construct($faker, array $defaultScopes, ?string $clientId, array $config, array $options = [])
    {
        $this->faker = $faker;
        $this->defaultScopes = $defaultScopes;
        parent::__construct($clientId, $config, $options);
    }

    /**
     * @var string[]
     */
    private $defaultScopes;

}