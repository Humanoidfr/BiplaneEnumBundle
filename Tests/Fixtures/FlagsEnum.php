<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\FlaggedEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlagsEnum extends FlaggedEnum
{
    public const FIRST = 1;
    public const SECOND = 2;
    public const THIRD = 4;
    public const FOURTH = 16;
    public const ALL = 23;

    public static function getReadables(): array
    {
        return [
            self::FIRST => 'First',
            self::SECOND => 'Second',
            self::THIRD => 'Third',
            self::FOURTH => 'Fourth',
        ];
    }

    public static function getPossibleValues(): array
    {
        return [self::FIRST, self::SECOND, self::THIRD, self::FOURTH];
    }
}
