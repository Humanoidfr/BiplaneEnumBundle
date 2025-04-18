<?php

namespace Biplane\EnumBundle\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Base enumeration of bit flags.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
abstract class FlaggedEnum extends Enum
{
    public const NONE = 0;

    private static array $masks = [];

    protected array $flags;

    public static function isAcceptableValue($value): bool
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Expected argument of type "integer", "%s" given.', is_object($value) ? get_class($value) : gettype($value)));
        }

        if (self::NONE === $value) {
            return true;
        }

        return $value === ($value & static::getBitmask());
    }

    public static function getReadableFor($value, $separator = '; '): string
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }

        if (self::NONE === $value) {
            return static::getReadableForNone();
        }

        $humanRepresentations = static::getReadables();

        if (isset($humanRepresentations[$value])) {
            return $humanRepresentations[$value];
        }

        $parts = [];

        foreach ($humanRepresentations as $flag => $readableValue) {
            if ($flag === ($flag & $value)) {
                $parts[] = $readableValue;
            }
        }

        return implode($separator, $parts);
    }

    /**
     * Gets the human representation for the none value.
     *
     * @return string
     */
    protected static function getReadableForNone(): string
    {
        return 'None';
    }

    /**
     * Gets an integer value of the possible flags for enumeration.
     *
     * @return int
     *
     * @throws \UnexpectedValueException
     */
    protected static function getBitmask(): int
    {
        $enumType = get_called_class();

        if (!isset(self::$masks[$enumType])) {
            $mask = 0;

            foreach (static::getPossibleValues() as $flag) {
                if ($flag < 1 || ($flag > 1 && ($flag % 2) !== 0)) {
                    throw new \UnexpectedValueException(sprintf('Possible value (%d) of the enumeration is not the bit flag.', $flag));
                }

                $mask |= $flag;
            }

            self::$masks[$enumType] = $mask;
        }

        return self::$masks[$enumType];
    }

    /**
     * Gets the bitmask of possible values.
     *
     * @return int
     *
     * @throws \UnexpectedValueException
     *
     * @deprecated
     */
    protected static function getMaskOfPossibleValues(): int
    {
        $mask = 0;

        foreach (static::getPossibleValues() as $flag) {
            if ($flag > 1 && ($flag % 2) !== 0) {
                throw new \UnexpectedValueException(sprintf('Possible value (%d) of the enumeration is not the bit flag.', $flag));
            }

            $mask |= $flag;
        }

        return $mask;
    }

    public function getReadable($separator = '; '): string
    {
        return static::getReadableFor($this->getValue(), $separator);
    }

    /**
     * Gets an array of bit flags of the value.
     *
     * @return array
     */
    public function getFlags(): array
    {
        if (null === $this->flags) {
            $this->flags = [];

            foreach (static::getPossibleValues() as $flag) {
                if ($this->hasFlag($flag)) {
                    $this->flags[] = $flag;
                }
            }
        }

        return $this->flags;
    }

    /**
     * Determines whether the specified flag is set in a numeric value.
     *
     * @param int $bitFlag the bit flag or bit flags
     *
     * @return bool True if the bit flag or bit flags are also set in the current instance; otherwise, false
     */
    public function hasFlag(int $bitFlag): bool
    {
        if ($bitFlag >= 1) {
            return $bitFlag === ($bitFlag & $this->value);
        }

        return false;
    }

    /**
     * Adds a bitmask to the value of this instance.
     *
     * Returns a new instance of this enumeration type.
     *
     * @param int $flags The bit flag or bit flags
     *
     * @return EnumInterface A new instance of the enumeration
     *
     * @throws InvalidEnumArgumentException When $flags is not acceptable for this enumeration type
     */
    public function addFlags(int $flags): EnumInterface
    {
        if (!static::isAcceptableValue($flags)) {
            throw new InvalidEnumArgumentException($flags);
        }

        return static::create($this->value | $flags);
    }

    /**
     * Removes a bitmask from the value of this instance.
     *
     * Returns a new instance of this enumeration type.
     *
     * @param int $flags The bit flag or bit flags
     *
     * @return EnumInterface A new instance of the enumeration
     *
     * @throws InvalidEnumArgumentException When $flags is not acceptable for this enumeration type
     */
    public function removeFlags(int $flags): EnumInterface
    {
        if (!static::isAcceptableValue($flags)) {
            throw new InvalidEnumArgumentException($flags);
        }

        return static::create($this->value & ~$flags);
    }
}
