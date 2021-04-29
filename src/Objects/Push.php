<?php


namespace Bytes\ResponseBundle\Objects;


/**
 * Class Push
 * @package Bytes\ResponseBundle\Objects
 */
class Push
{
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
                if (!is_null($key)) {
                    $this->array[$key] = $value;
                } else {
                    $this->array[] = $value;
                }
            }
        } else {
            if (!is_null($value)) {
                if (!is_null($key)) {
                    $this->array[$key] = $value;
                } else {
                    $this->array[] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Get the array
     * @return array
     */
    public function value()
    {
        return $this->array;
    }
}