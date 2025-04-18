<?php

namespace Biplane\EnumBundle\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Enumeration interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Denis Vasilev <yethee@biplane.ru>
 */
interface EnumInterface
{
    /**
     * Instanciates a new enumeration.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @return EnumInterface A new instance of an enum
     *
     * @throws InvalidEnumArgumentException When $value is not acceptable for this enumeration type
     */
    public static function create($value): EnumInterface;

    /**
     * Gets an array of the possible values.
     */
    public static function getPossibleValues(): array;

    /**
     * Gets an array of the human representations indexed by possible values.
     */
    public static function getReadables(): array;

    /**
     * Tells is this value is acceptable.
     *
     * @return bool True if $value is acceptable for this enumeration type; otherwise false
     */
    public static function isAcceptableValue($value): bool;

    /**
     * Gets the human representation for a given value.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @return string The human representation for a given value
     *
     * @throws InvalidEnumArgumentException When $value is not acceptable for this enumeration type
     */
    public static function getReadableFor($value): string;

    /**
     * Gets the raw value.
     */
    public function getValue();

    /**
     * Gets the human representation of the value.
     */
    public function getReadable(): string;

    /**
     * Determines whether enums are equals.
     *
     * @param EnumInterface $enum An enum object to compare with this instance
     *
     * @return bool True if $enum is an enum with the same type and value as this instance; otherwise, false
     */
    public function equals(EnumInterface $enum): bool;
}
