<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Enumeration\FlaggedEnum;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a bit flags and the flagged enumeration instance.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlaggedEnumToValuesTransformer extends BaseEnumTransformer
{
    /**
     * Transforms a FlaggedEnum objects to an array of bit flags.
     *
     * @param FlaggedEnum $value A FlaggedEnum instance
     *
     * @return array An array of bit flags
     *
     * @throws UnexpectedTypeException When $value is not the flagged enumeration
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof $this->enumClass) {
            throw new UnexpectedTypeException($value, $this->enumClass);
        }

        return $value->getFlags();
    }

    /**
     * Transforms an array of raw values to the flagged enumeration object.
     *
     * @param array $value An array of raw values
     *
     * @return EnumInterface A FlaggedEnum instance or null
     *
     * @throws UnexpectedTypeException       When $values is not array
     * @throws TransformationFailedException When any value is not the integer type
     */
    public function reverseTransform($value): EnumInterface
    {
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (0 == count($value)) {
            return $this->createEnum(FlaggedEnum::NONE);
        }

        $rawValue = 0;

        foreach ($value as $val) {
            if (!is_integer($val)) {
                throw new TransformationFailedException(sprintf('The value "%s" (type of %s) must be the integer type.', $val, gettype($val)));
            }

            $rawValue |= $value;
        }

        return $this->createEnum($rawValue);
    }
}
