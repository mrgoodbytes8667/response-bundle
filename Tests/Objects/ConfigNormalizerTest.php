<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Objects\ConfigNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigNormalizerTest
 * @package Bytes\ResponseBundle\Tests\Objects
 */
class ConfigNormalizerTest extends TestCase
{
    use TestFakerTrait;

    /**
     *
     */
    public function testNormalizeEndpoints()
    {
        $key1 = $this->faker->unique()->word();
        $key2 = $this->faker->unique()->word();
        $extraKey = $this->faker->unique()->word();
        
        $config = [
            'sample' => [],
            'endpoints' => [
                $key1 => [],
                $extraKey => [],
            ]
        ];

        $normalized = ConfigNormalizer::normalizeEndpoints($config, [$key1, $key2]);
        $this->assertArrayHasKey('sample', $normalized);
        $this->assertArrayHasKey('endpoints', $normalized);

        $normalized = $normalized['endpoints'];

        $this->assertArrayNotHasKey($extraKey, $normalized);

        $this->extracted($key1, $normalized);
        $this->extracted($key2, $normalized);

    }

    /**
     * @param string $key
     * @param array $normalized
     */
    protected function extracted(string $key, mixed $normalized): void
    {
        $this->assertArrayHasKey($key, $normalized);

        $this->assertArrayHasKey('redirects', $normalized[$key]);
        $this->assertArrayHasKey('method', $normalized[$key]['redirects']);
        $this->assertArrayHasKey('route_name', $normalized[$key]['redirects']);
        $this->assertArrayHasKey('url', $normalized[$key]['redirects']);

        $this->assertArrayHasKey('permissions', $normalized[$key]);
        $this->assertArrayHasKey('add', $normalized[$key]['permissions']);
        $this->assertArrayHasKey('remove', $normalized[$key]['permissions']);
        $this->assertArrayHasKey('scopes', $normalized[$key]);
        $this->assertArrayHasKey('add', $normalized[$key]['scopes']);
        $this->assertArrayHasKey('remove', $normalized[$key]['scopes']);
    }
}
