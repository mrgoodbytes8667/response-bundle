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
        $endpointKey1 = $this->faker->unique()->word();
        $endpointKey2 = $this->faker->unique()->word();
        $endpointExtraKey = $this->faker->unique()->word();

        $config = [
            'sample' => [],
            'endpoints' => [
                $endpointKey1 => [],
                $endpointExtraKey => [],
            ]
        ];

        $normalized = ConfigNormalizer::normalizeEndpoints($config, [$endpointKey1, $endpointKey2]);
        $this->assertArrayHasKey('sample', $normalized);
        $this->assertArrayHasKey('endpoints', $normalized);

        $normalized = $normalized['endpoints'];

        $this->assertArrayNotHasKey($endpointExtraKey, $normalized);

        $this->extracted($endpointKey1, $normalized, ['permissions', 'scopes']);
        $this->extracted($endpointKey2, $normalized, ['permissions', 'scopes']);

    }

    /**
     * @param string $key
     * @param array $normalized
     * @param array $addRemoveParentKeys
     */
    protected function extracted(string $key, mixed $normalized, array $addRemoveParentKeys): void
    {
        $this->assertArrayHasKey($key, $normalized);

        $this->assertArrayHasKey('redirects', $normalized[$key]);
        $this->assertArrayHasKey('method', $normalized[$key]['redirects']);
        $this->assertArrayHasKey('route_name', $normalized[$key]['redirects']);
        $this->assertArrayHasKey('url', $normalized[$key]['redirects']);

        foreach ($addRemoveParentKeys as $addRemoveParentKey) {
            $this->assertArrayHasKey($addRemoveParentKey, $normalized[$key]);
            $this->assertArrayHasKey('add', $normalized[$key][$addRemoveParentKey]);
            $this->assertArrayHasKey('remove', $normalized[$key][$addRemoveParentKey]);
        }
    }

    /**
     *
     */
    public function testNormalizeEndpointsWithAddRemove()
    {
        $endpointKey1 = $this->faker->unique()->word();
        $endpointKey2 = $this->faker->unique()->word();
        $endpointExtraKey = $this->faker->unique()->word();
        $addRemoveParentKeys = $this->faker->unique()->words();
        $addRemoveParentExtraKey = $this->faker->valid(function ($value) use ($addRemoveParentKeys, $endpointExtraKey, $endpointKey2, $endpointKey1) {
            return !in_array($value, [$endpointKey1, $endpointKey2, $endpointExtraKey]) && !in_array($value, $addRemoveParentKeys);
        })->word();

        $config = [
            'sample' => [],
            'endpoints' => [
                $endpointKey1 => [
                    $addRemoveParentExtraKey => []
                ],
                $endpointExtraKey => [],
            ]
        ];

        $normalized = ConfigNormalizer::normalizeEndpoints($config, [$endpointKey1, $endpointKey2], $addRemoveParentKeys);
        $this->assertArrayHasKey('sample', $normalized);
        $this->assertArrayHasKey('endpoints', $normalized);

        $normalized = $normalized['endpoints'];

        $this->assertArrayNotHasKey($endpointExtraKey, $normalized);

        $this->extracted($endpointKey1, $normalized, $addRemoveParentKeys);
        $this->extracted($endpointKey2, $normalized, $addRemoveParentKeys);

        // Verify extra addRemove key is gone
        $this->assertArrayNotHasKey($addRemoveParentExtraKey, $normalized[$endpointKey1]);
    }
}