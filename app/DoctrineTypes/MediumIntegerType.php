<?php
namespace App\DoctrineTypes;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;


// Define the custom type class
class MediumIntegerType extends \Doctrine\DBAL\Types\IntegerType
{
    public function getName()
    {
        return 'mediuminteger';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'MEDIUMINT' . ($fieldDeclaration['unsigned'] ? ' UNSIGNED' : '') . ' COMMENT ' . $platform->quoteStringLiteral($fieldDeclaration['comment']);
    }
}

