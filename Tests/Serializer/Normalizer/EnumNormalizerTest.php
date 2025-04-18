<?php

namespace Biplane\EnumBundle\Tests\Serializer\Normalizer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Serializer\Normalizer\EnumNormalizer;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testNormalize(): void
    {
        $normalizer = new EnumNormalizer();
        $value = SimpleEnum::create(SimpleEnum::FIRST);

        self::assertSame($value->getValue(), $normalizer->normalize($value));
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalize(): void
    {
        $normalizer = new EnumNormalizer();

        $result = $normalizer->denormalize(1, SimpleEnum::class);

        self::assertInstanceOf(SimpleEnum::class, $result);
        self::assertSame(1, $result->getValue());
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new EnumNormalizer();

        self::assertTrue($normalizer->supportsNormalization(SimpleEnum::create(SimpleEnum::FIRST)));
        self::assertFalse($normalizer->supportsNormalization(null));
    }

    /**
     * @throws \ReflectionException
     */
    public function testSupportsDenormalization(): void
    {
        $normalizer = new EnumNormalizer();

        self::assertTrue($normalizer->supportsDenormalization(1, SimpleEnum::class));
        self::assertFalse($normalizer->supportsDenormalization('1', SimpleEnum::class));
        self::assertFalse($normalizer->supportsDenormalization(null, __CLASS__));
    }
}
