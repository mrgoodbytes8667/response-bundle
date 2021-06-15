<?php

namespace Bytes\ResponseBundle\Tests\Routing;

use Bytes\Common\Faker\Providers\MiscProvider;
use Bytes\ResponseBundle\Routing\AbstractOAuth;
use Bytes\ResponseBundle\Tests\Fixtures\Routing\AbstractModel;
use Bytes\ResponseBundle\Tests\Fixtures\Routing\Model;
use Bytes\ResponseBundle\Tests\Fixtures\Routing\User;
use Bytes\Tests\Common\TestFullValidatorTrait;
use Faker\Factory;
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
use Generator;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class AbstractOAuthTest
 * @package Bytes\ResponseBundle\Tests\Routing
 */
class AbstractOAuthTest extends TestCase
{
    use TestFullValidatorTrait;

    /**
     * @var FakerGenerator|MiscProvider|Address|Barcode|Biased|Color|Company|DateTime|File|HtmlLorem|Image|Internet|Lorem|Medical|Miscellaneous|Payment|Person|PhoneNumber|Text|UserAgent|Uuid
     */
    protected $faker;
    /**
     * @var string[]
     */
    private $defaultScopes;
    /**
     * @var string
     */
    private $clientId;

    /**
     *
     */
    public function setUp(): void
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new MiscProvider($this->faker));
        $this->defaultScopes = $this->faker->unique()->words(3);
    }

    /**
     *
     */
    public function tearDown(): void
    {
        $this->defaultScopes = null;
    }

    /**
     *
     */
    public function testGetAuthorizationUrl()
    {
        $oauth = $this->setupOAuth();

        $output = $oauth->getAuthorizationURL();

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('client_id=' . $this->clientId, $output);
        $this->assertStringContainsString('response_type=code', $output);
        $this->assertStringContainsString('scope=' . urlencode(implode(' ', $this->defaultScopes)), $output);
        $this->assertStringContainsString('random=normalizedPrompt', $output);
        $this->assertStringStartsWith('?', $output);
        $query = explode('&', $output);
        $this->assertCount(6, $query);

    }

    /**
     * @param array|null $config
     * @param bool $setValidator
     * @return AbstractOAuth
     */
    public function setupOAuth(array $config = null, bool $setValidator = true): AbstractOAuth
    {
        if (is_null($config)) {
            $config = [
                'model' => [
                    'scopes' => [
                        'add' => [],
                        'remove' => [],
                    ],
                    'redirects' => [
                        'method' => 'url',
                        'url' => 'https://www.example.com'
                    ]
                ]
            ];
        }

        $this->clientId = $this->faker->randomAlphanumericString();
        $mock = new Model($this->faker, $this->defaultScopes, $this->clientId, $config);
        if ($setValidator) {
            $mock->setValidator($this->validator);
        }
        $csrf = $this->getMockBuilder(CsrfTokenManagerInterface::class)->getMock();
        $csrf->method('getToken')->willReturn('abc123');
        $mock->setCsrfTokenManager($csrf);

        //$mock = $this->getMockForAbstractClass(Model::class, [$this->faker, $this->defaultScopes, $this->faker->randomAlphanumericString(), $config]);

        return $mock;
    }

    /**
     *
     */
    public function testGetAuthorizationUrlWithPrompt()
    {
        $oauth = $this->setupOAuth();

        $output = $oauth->getAuthorizationURL(prompt: $this->faker->word());

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('client_id=' . $this->clientId, $output);
        $this->assertStringContainsString('response_type=code', $output);
        $this->assertStringContainsString('scope=' . urlencode(implode(' ', $this->defaultScopes)), $output);
        $this->assertStringContainsString('random=normalizedPrompt', $output);
        $this->assertStringStartsWith('?', $output);
        $query = explode('&', $output);
        $this->assertCount(6, $query);

    }

    /**
     *
     */
    public function testGetRedirect()
    {
        $oauth = $this->setupOAuth();
        $redirect = $oauth->getRedirect();
        $this->assertEquals('https://www.example.com', $redirect);
    }

    /**
     *
     */
    public function testGetRedirectBypassConstructor()
    {
        $oauth = $this->getMockForAbstractClass(AbstractModel::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The key "model" was not present in the configuration');
        $oauth->getRedirect();
    }

    /**
     *
     */
    public function testGetRedirectNoRedirects()
    {
        $oauth = $this->setupOAuth(['model' => []]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The configuration for key "model" was not valid');
        $oauth->getRedirect();
    }

    /**
     *
     */
    public function testGetRedirectRouteWithoutGenerator()
    {
        $oauth = $this->setupOAuth(['model' => ['redirects' => ['method' => 'route_name']]]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('URLGeneratorInterface cannot be null when a route name is passed');
        $oauth->getRedirect();
    }

    /**
     *
     */
    public function testGetRedirectRouteNotFound()
    {
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();

        $urlGenerator->method('generate')
            ->willThrowException(new RouteNotFoundException());

        $oauth = $this->setupOAuth(['model' => ['redirects' => ['method' => 'route_name', 'route_name' => $this->faker->word()]]]);
        $oauth->setUrlGenerator($urlGenerator);
        $this->expectException(RouteNotFoundException::class);
        $oauth->getRedirect();
    }

    /**
     *
     */
    public function testGetRedirectInvalidMethod()
    {
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();

        $urlGenerator->method('generate')
            ->willThrowException(new RouteNotFoundException());

        $oauth = $this->setupOAuth(['model' => ['redirects' => ['method' => $this->faker->word()]]]);
        $oauth->setUrlGenerator($urlGenerator);
        $this->expectException(InvalidArgumentException::class);
        $oauth->getRedirect();
    }

    /**
     *
     */
    public function testGetRedirectViaGeneratorRoute()
    {
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();

        $expectedRedirect = $this->faker->url();

        $urlGenerator->method('generate')
            ->willReturn($expectedRedirect);

        $oauth = $this->setupOAuth(['model' => ['redirects' => ['method' => 'route_name', 'route_name' => $this->faker->word()]]]);
        $oauth->setUrlGenerator($urlGenerator);
        $redirect = $oauth->getRedirect();
        $this->assertEquals($expectedRedirect, $redirect);
    }

    /**
     *
     */
    public function testGetRedirectNoValidator()
    {
        $oauth = $this->setupOAuth(setValidator: false);
        $this->expectException(LogicException::class);
        $oauth->getRedirect();
    }

    /**
     *
     */
    public function testGetScopes()
    {
        $oauth = $this->setupOAuth();

        $this->assertCount(3, $oauth->getScopes($this->faker->valid(function ($values) {
            foreach ($values as $value) {
                if (in_array($value, $this->defaultScopes)) {
                    return false;
                }
            }
            return true;
        })->words(3)));

        $this->assertCount(3, $oauth->getScopes($this->faker->valid(function ($values) {
            foreach ($values as $value) {
                if (in_array($value, $this->defaultScopes)) {
                    return false;
                }
            }
            return true;
        })->words(3)));
    }

    /**
     * @dataProvider provideRange
     * @param $index
     */
    public function testGetScopes2($index)
    {
        foreach ($this->provideScopes() as $scope) {
            $add = $scope['add'];
            $remove = $scope['remove'];
            $scopes = $scope['scopes'];

            $oauth = $this->setupOAuth([
                'model' => [
                    'scopes' => [
                        'add' => $add,
                        'remove' => $remove,
                    ]
                ]
            ]);

            $normalizedScopes = $oauth->getScopes($scopes);

            $this->assertCount(6, $normalizedScopes);
        }
    }

    /**
     * @return Generator
     */
    public function provideScopes()
    {
        $defaults = $this->defaultScopes;

        $words = array_unique($this->faker->unique()->words(30));

        $add = [];
        $remove = [];
        $scopes = [];

        do {
            $temp = array_shift($words);
            if (!in_array($temp, $defaults)) {
                $add[] = $temp;
            }
        } while (count($add) < 3);

        do {
            $temp = array_shift($words);
            if (!in_array($temp, $defaults)) {
                $remove[] = $temp;
            }
        } while (count($remove) < 3);

        do {
            $temp = array_shift($words);
            if (!in_array($temp, $defaults)) {
                $scopes[] = $temp;
            }
        } while (count($scopes) < 3);

        yield ['defaults' => $defaults, 'add' => $add, 'remove' => $remove, 'scopes' => $scopes];
    }

    /**
     * @return Generator
     */
    public function provideRange()
    {
        foreach (range(0, 50) as $index) {
            yield [$index];
        }
    }

    /**
     *
     */
    public function testGetScopes3()
    {
        $oauth = $this->setupOAuth(['model' => []]);

        $this->assertCount(3, $oauth->getScopes($this->faker->valid(function ($values) {
            foreach ($values as $value) {
                if (in_array($value, $this->defaultScopes)) {
                    return false;
                }
            }
            return true;
        })->words(3)));
    }

    /**
     *
     */
    public function testGetAuthorizationCodeGrantURL()
    {
        $oauth = $this->setupOAuth();

        $redirect = $this->faker->url();
        $state = $this->faker->randomAlphanumericString();
        $output = $oauth->getAuthorizationCodeGrantURL($redirect, $this->defaultScopes, $state);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('client_id=' . $this->clientId, $output);
        $this->assertStringContainsString('redirect_uri=' . urlencode($redirect), $output);
        $this->assertStringContainsString('response_type=code', $output);
        $this->assertStringContainsString('scope=' . urlencode(implode(' ', $this->defaultScopes)), $output);
        $this->assertStringContainsString('state=' . $state, $output);
        $this->assertStringContainsString('random=normalizedPrompt', $output);
        $this->assertStringStartsWith('?', $output);
        $query = explode('&', $output);
        $this->assertCount(6, $query);
    }

    /**
     *
     */
    public function testGetAuthorizationCodeGrantURLNoStateSecurity()
    {
        $oauth = $this->setupOAuth();

        $userId = $this->faker->randomAlphanumericString();

        $user = new User($userId);

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->method('getUser')
            ->willReturn($user);

        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $security->method('getToken')
            ->willReturn($token);

        $oauth->setSecurity($security);

        $redirect = $this->faker->url();
        $state = $userId;
        $output = $oauth->getAuthorizationCodeGrantURL($redirect, $this->defaultScopes, null);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('client_id=' . $this->clientId, $output);
        $this->assertStringContainsString('redirect_uri=' . urlencode($redirect), $output);
        $this->assertStringContainsString('response_type=code', $output);
        $this->assertStringContainsString('scope=' . urlencode(implode(' ', $this->defaultScopes)), $output);
        $this->assertStringContainsString('state=' . $state, $output);
        $this->assertStringContainsString('random=normalizedPrompt', $output);
        $this->assertStringStartsWith('?', $output);
        $query = explode('&', $output);
        $this->assertCount(6, $query);
    }

    /**
     *
     */
    public function testSetSecurity()
    {
        $oauth = $this->setupOAuth();
        $this->assertInstanceOf(AbstractOAuth::class, $oauth->setSecurity(null));
    }
}