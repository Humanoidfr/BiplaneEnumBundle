<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between raw values and enumeration instances.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumsToValuesTransformer extends BaseEnumTransformer
{
    /**
     * Transforms an array of raw values to enumeration objects.
     *
     * @param array $value An array of raw values
     *
     * @return EnumInterface[] An array of EnumInterface instances
     *
     * @throws UnexpectedTypeException       When $values is not array
     * @throws TransformationFailedException When any value is not acceptable for enumeration
     */
    public function reverseTransform($value): array
    {
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        try {
            return array_map([$this, 'createEnum'], $value);
        } catch (InvalidEnumArgumentException $ex) {
            throw new TransformationFailedException(sprintf('One or more values is not acceptable for enumeration of %s type.', $this->enumClass));
        }
    }

    /**
     * Transforms an array of enumeration objects to a raw values.
     *
     * @param EnumInterface[] $value An array of EnumInterface instances
     *
     * @return array An array of raw values
     *
     * @throws UnexpectedTypeException       When $values is not array or the FlaggedEnum instance
     * @throws TransformationFailedException When any value is not instance of the enumeration
     */
    public function transform($value): array
    {
        if (null === $value) {
            return [];
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $result = [];

        foreach ($value as $val) {
            if (!$val instanceof $this->enumClass) {
                throw new TransformationFailedException(sprintf('Could not convert a value of type "%s" to choice, it is to be an instance of %s.', is_object($val) ? get_class($val) : gettype($val), $this->enumClass));
            }

            $result[] = $val->getValue();
        }

        return $result;
    }
}
