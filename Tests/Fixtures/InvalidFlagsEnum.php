<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\FlaggedEnum as BaseFlagEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class InvalidFlagsEnum extends BaseFlagEnum
{
    public const FIRST = 1;
    public const SECOND = 2;
    public const INVALID = 3;

    public static function getReadables(): array
    {
        return [
            self::FIRST => 'First',
            self::SECOND => 'Second',
            self::INVALID => 'Invalid',
        ];
    }

    public static function getPossibleValues(): array
    {
        return [self::FIRST, self::SECOND, self::INVALID];
    }
}
