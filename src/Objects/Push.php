<?php


namespace Bytes\ResponseBundle\Objects;


use function Symfony\Component\String\u;

/**
 * Class Push
 * @package Bytes\ResponseBundle\Objects
 */
class Push
{
    /**
     * Holds the (cached) camel key version of the array
     * Cleared upon every call to push() that adds/updates a row
     * @var array
     */
    private $camelArray = [];

    /**
     * Holds the (cached) snake key version of the array
     * Cleared upon every call to push() that adds/updates a row
     * @var array
     */
    private $snakeArray = [];

    /**
     * Push constructor.
     * @param array $array
     */
    public function __construct(private array $array = [])
    {
    }

    /**
     * Creates the object [from an existing array] and does a single push()
     * @param array|null $array
     * @param mixed|null $value
     * @param int|string|null $key
     * @param bool $empty
     * @return Push
     */
    public static function createPush(?array $array = [], $value = null, int|string|null $key = null, bool $empty = true)
    {
        $static = static::create($array);
        return $static->push($value, $key, $empty);
    }

    /**
     * Creates the object [from an existing array]
     * @param array|null $array (Optional) array to seed the object from
     * @return static
     */
    public static function create(?array $array = [])
    {
        return new static($array ?? []);
    }

    /**
     * Push if value is not null/empty
     * @param mixed|null $value Value to push
     * @param int|string|null $key Key to push
     * @param bool $empty When true, ignores the push if the value is empty. Defaults to true.
     * @return $this
     */
    public function push($value = null, int|string|null $key = null, bool $empty = true): self
    {
        if ($empty) {
            if (!empty($value)) {
                $this->update($value, $key);
            }
        } else {
            if (!is_null($value)) {
                $this->update($value, $key);
            }
        }

        return $this;
    }

    /**
     * @param null $value
     * @param int|string|null $key
     */
    private function update($value = null, int|string|null $key = null)
    {
        if (!is_null($key)) {
            $this->array[$key] = $value;
            $this->resetCachedArrays();
        } else {
            $this->array[] = $value;
        }
    }

    /**
     * Reset all cached arrays
     */
    private function resetCachedArrays(): void
    {
        $this->camelArray = [];
        $this->snakeArray = [];
    }

    /**
     * Get the array
     * @return array
     */
    public function value()
    {
        return $this->array;
    }

    /**
     * Get the array with each key transformed into camel case
     * @return array
     */
    public function camel()
    {
        if (!empty($this->camelArray)) {
            return $this->camelArray;
        }

        foreach ($this->array as $index => $value) {
            $key = u($index)->camel()->toString();
            $this->camelArray[$key] = $value;
        }

        return $this->camelArray;
    }

    /**
     * Get the array with each key transformed into snake case
     * @return array
     */
    public function snake()
    {
        if (!empty($this->snakeArray)) {
            return $this->snakeArray;
        }

        foreach ($this->array as $index => $value) {
            $key = u($index)->snake()->toString();
            $this->snakeArray[$key] = $value;
        }

        return $this->snakeArray;
    }
}