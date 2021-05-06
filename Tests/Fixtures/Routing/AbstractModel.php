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

abstract class AbstractModel extends \Bytes\ResponseBundle\Routing\AbstractOAuth
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
     * AbstractModel constructor.
     */
    public function __construct()
    {
        $this->config = [];
    }

    /**
     * @var string[]
     */
    private $defaultScopes;

}