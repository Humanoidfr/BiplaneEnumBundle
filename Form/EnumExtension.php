<?php

namespace Biplane\EnumBundle\Form;

use Symfony\Component\Form\AbstractExtension;

/**
 * EnumExtension.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumExtension extends AbstractExtension
{
    protected function loadTypes(): array
    {
        return [
            new Type\EnumType(),
        ];
    }
}
