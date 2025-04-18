<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * BaseEnumTransformer.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
abstract class BaseEnumTransformer implements DataTransformerInterface
{
    protected string $enumClass;

    /**
     * Constructor.
     *
     * @param string $enumClass A full class name of enumeration
     *
     * @throws \InvalidArgumentException|\ReflectionException When $enumClass not implement the EnumInterface
     */
    public function __construct(string $enumClass)
    {
        $reflection = new \ReflectionClass($enumClass);

        if (!$reflection->implementsInterface(EnumInterface::class)) {
            throw new \InvalidArgumentException(sprintf('Enum class "%s" must be implements of %s', $enumClass, EnumInterface::class));
        }

        $this->enumClass = $reflection->getName();
    }

    /**
     * Creates the enum object for this value.
     *
     * @param mixed $value A raw value
     *
     * @return EnumInterface
     */
    protected function createEnum($value): EnumInterface
    {
        return call_user_func([$this->enumClass, 'create'], $value);
    }
}
