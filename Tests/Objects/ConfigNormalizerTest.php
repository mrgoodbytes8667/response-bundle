<?php

namespace Bytes\ResponseBundle\Tests\Objects;

use Bytes\Common\Faker\TestFakerTrait;
use Bytes\ResponseBundle\Objects\ConfigNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigNormalizerTest.
 */
class ConfigNormalizerTest extends TestCase
{
    use TestFakerTrait;

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
            ],
        ];

        $normalized = ConfigNormalizer::normalizeEndpoints($config, [$endpointKey1, $endpointKey2]);
        self::assertArrayHasKey('sample', $normalized);
        self::assertArrayHasKey('endpoints', $normalized);

        $normalized = $normalized['endpoints'];

        self::assertArrayNotHasKey($endpointExtraKey, $normalized);

        $this->extracted($endpointKey1, $normalized, ['permissions', 'scopes']);
        $this->extracted($endpointKey2, $normalized, ['permissions', 'scopes']);
    }

    /**
     * @param array $normalized
     */
    protected function extracted(string $key, mixed $normalized, array $addRemoveParentKeys): void
    {
        self::assertArrayHasKey($key, $normalized);

        self::assertArrayHasKey('redirects', $normalized[$key]);
        self::assertArrayHasKey('method', $normalized[$key]['redirects']);
        self::assertArrayHasKey('route_name', $normalized[$key]['redirects']);
        self::assertArrayHasKey('url', $normalized[$key]['redirects']);

        foreach ($addRemoveParentKeys as $addRemoveParentKey) {
            self::assertArrayHasKey($addRemoveParentKey, $normalized[$key]);
            self::assertArrayHasKey('add', $normalized[$key][$addRemoveParentKey]);
            self::assertArrayHasKey('remove', $normalized[$key][$addRemoveParentKey]);
        }
    }

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
                    $addRemoveParentExtraKey => [],
                ],
                $endpointExtraKey => [],
            ],
        ];

        $normalized = ConfigNormalizer::normalizeEndpoints($config, [$endpointKey1, $endpointKey2], $addRemoveParentKeys);
        self::assertArrayHasKey('sample', $normalized);
        self::assertArrayHasKey('endpoints', $normalized);

        $normalized = $normalized['endpoints'];

        self::assertArrayNotHasKey($endpointExtraKey, $normalized);

        $this->extracted($endpointKey1, $normalized, $addRemoveParentKeys);
        $this->extracted($endpointKey2, $normalized, $addRemoveParentKeys);

        // Verify extra addRemove key is gone
        self::assertArrayNotHasKey($addRemoveParentExtraKey, $normalized[$endpointKey1]);
    }
}
