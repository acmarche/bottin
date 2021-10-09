<?php

namespace AcMarche\Bottin\Cbe\Import;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Make a string's first character lowercase.
 */
class LowerNameConverter implements NameConverterInterface
{
    public function normalize(string $propertyName)
    {
        return lcfirst($propertyName);
    }

    public function denormalize(string $propertyName)
    {
        return lcfirst($propertyName);
    }
}
