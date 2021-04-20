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
     */
    public function __construct(private array $array = [])
    {
    }

    /**
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
     * @param array|null $array
     * @return static
     */
    public static function create(?array $array = [])
    {
        return new static($array ?? []);
    }

    /**
     * Push if value is not null/empty
     * @param mixed|null $value
     * @param int|string|null $key
     * @param bool $empty
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
     * @return array
     */
    public function value()
    {
        return $this->array;
    }
}