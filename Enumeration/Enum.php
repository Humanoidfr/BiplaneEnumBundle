<?php

namespace Biplane\EnumBundle\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Base class of enumeration.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Enum implements EnumInterface
{
    protected $value;

    /**
     * The constructor is protected: use the static create method instead.
     *
     * @param mixed $value The raw value of an enumeration
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    public static function create($value): EnumInterface
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }

        return new static($value);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getReadable(): string
    {
        return static::getReadableFor($this->getValue());
    }

    /**
     * Converts to the human representation of the current value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getReadable();
    }

    public static function isAcceptableValue($value): bool
    {
        return in_array($value, static::getPossibleValues(), true);
    }

    public static function getReadableFor($value): string
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }

        $humanRepresentations = static::getReadables();

        return $humanRepresentations[$value];
    }

    public function equals(EnumInterface $enum): bool
    {
        return get_class($this) === get_class($enum) && $this->value === $enum->getValue();
    }
}
