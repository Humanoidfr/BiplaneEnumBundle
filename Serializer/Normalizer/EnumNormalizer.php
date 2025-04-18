<?php

namespace Biplane\EnumBundle\Serializer\Normalizer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * EnumNormalizer.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue();
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return call_user_func([$type, 'create'], $data);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof EnumInterface;
    }

    /**
     * @throws \ReflectionException
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        $reflection = new \ReflectionClass($type);

        if ($reflection->isSubclassOf(EnumInterface::class)) {
            if (call_user_func([$type, 'isAcceptableValue'], $data)) {
                return true;
            }
        }

        return false;
    }
}
