<?php

namespace Bytes\ResponseBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use PHPUnit\Framework\TestCase;

class TaggedLocatorTest extends TestCase
{
    public function testLocatorUsesAsTaggedItemIndex(): void
    {
        $container = new ContainerBuilder();
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../src/Resources/config'));
        $loader->load('services.php');
        $container->register('serializer', \stdClass::class);
        $container->register(TaggedOAuthService::class)
            ->setAutowired(false)
            ->setAutoconfigured(true)
            ->addTag('bytes_response.oauth');
        $container->compile();

        $locator = $container->get('bytes_response.locator.oauth');

        self::assertTrue($locator->has('sample-oauth'));
        self::assertSame(TaggedOAuthService::class, $locator->get('sample-oauth')::class);
    }

    /**
     * @dataProvider locatorProvider
     */
    public function testLocatorsUseAttributeBasedIndexes(string $serviceId, string $tag, string $indexAttribute, string $defaultIndexMethod, string $defaultPriorityMethod): void
    {
        $container = new ContainerBuilder();
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../src/Resources/config'));
        $loader->load('services.php');

        $argument = $container->getDefinition($serviceId)->getArgument(0);

        self::assertInstanceOf(ServiceLocatorArgument::class, $argument);
        self::assertInstanceOf(TaggedIteratorArgument::class, $argument->getTaggedIteratorArgument());
        self::assertSame($tag, $argument->getTaggedIteratorArgument()->getTag());
        self::assertSame($indexAttribute, $argument->getTaggedIteratorArgument()->getIndexAttribute());
        self::assertSame($defaultIndexMethod, $argument->getTaggedIteratorArgument()->getDefaultIndexMethod());
        self::assertSame($defaultPriorityMethod, $argument->getTaggedIteratorArgument()->getDefaultPriorityMethod());
    }

    public static function locatorProvider(): iterable
    {
        yield ['bytes_response.locator.http_client', 'bytes_response.http_client', 'http_client', 'getDefaultHttpClientName', 'getDefaultHttpClientPriority'];
        yield ['bytes_response.locator.http_client.api', 'bytes_response.http_client.api', 'api', 'getDefaultApiName', 'getDefaultApiPriority'];
        yield ['bytes_response.locator.http_client.token', 'bytes_response.http_client.token', 'token', 'getDefaultTokenName', 'getDefaultTokenPriority'];
        yield ['bytes_response.locator.oauth', 'bytes_response.oauth', 'oauth', 'getDefaultOauthName', 'getDefaultOauthPriority'];
        yield ['bytes_response.locator.authenticator.oauth', 'bytes_response.authenticator.oauth', 'oauth', 'getDefaultOauthName', 'getDefaultOauthPriority'];
    }
}

#[AsTaggedItem(index: 'sample-oauth')]
class TaggedOAuthService
{
    public function setUrlGenerator(): void
    {
    }

    public function setValidator(): void
    {
    }

    public function setSecurity(): void
    {
    }

    public function setCsrfTokenManager(): void
    {
    }
}
