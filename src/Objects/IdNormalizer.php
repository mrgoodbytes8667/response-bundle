<?php


namespace Bytes\ResponseBundle\Objects;


use Bytes\ResponseBundle\Interfaces\IdInterface;
use InvalidArgumentException;

/**
 * Class IdNormalizer
 * @package Bytes\ResponseBundle\Objects
 */
class IdNormalizer
{
    /**
     * Return getId() on object that implements IdInterface, or the string value if $object is a string.
     * @param IdInterface|string $object
     * @param string $message
     * @param bool $allowNull
     * @return string|null
     */
    public static function normalizeIdArgument($object, string $message = '', bool $allowNull = false)
    {
        if ($allowNull && is_null($object)) {
            return null;
        }
        if (empty($message)) {
            if (is_object($object)) {
                $message = sprintf('The "%s" argument is required.', get_class($object));
            } else {
                $message = 'The argument is required.';
            }
        }
        $id = '';
        if (is_null($object)) {
            throw new InvalidArgumentException($message);
        }
        if ($object instanceof IdInterface) {
            $id = $object->getId();
        } elseif (is_string($object)) {
            $id = $object;
        } elseif (is_int($object))
        {
            $id = (string)$object;
        }
        if (empty($id) && !$allowNull) {
            throw new InvalidArgumentException($message);
        }
        return $id;
    }
}