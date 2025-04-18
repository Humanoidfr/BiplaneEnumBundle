<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\Enum;

class SimpleEnum extends Enum
{
    public const ZERO = 0;
    public const FIRST = 1;
    public const SECOND = 2;

    public static function getReadables(): array
    {
        return [
            self::ZERO => 'Zero',
            self::FIRST => 'First',
            self::SECOND => 'Second',
        ];
    }

    public static function getPossibleValues(): array
    {
        return [self::ZERO, self::FIRST, self::SECOND];
    }
}
