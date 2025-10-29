<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Internal\SQLResultCasing;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\JoinColumnMapping;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;

class QuoteStrategy implements \Doctrine\ORM\Mapping\QuoteStrategy
{
    use SQLResultCasing;

    public function getColumnName(
        string $fieldName,
        ClassMetadata $class,
        AbstractPlatform $platform,
    ): string {
        return $class->fieldMappings[$fieldName]->columnName;
    }

    public function getTableName(ClassMetadata $class, AbstractPlatform $platform): string
    {
        return '"'.$class->table['name'].'"';
    }

    public function getSequenceName(array $definition, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $definition['sequenceName'];
    }

    public function getJoinColumnName(JoinColumnMapping $joinColumn, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $joinColumn->name;
    }

    public function getReferencedJoinColumnName(
        JoinColumnMapping $joinColumn,
        ClassMetadata $class,
        AbstractPlatform $platform,
    ): string {
        return $joinColumn->referencedColumnName;
    }

    public function getJoinTableName(
        ManyToManyOwningSideMapping $association,
        ClassMetadata $class,
        AbstractPlatform $platform,
    ): string {
        return $association->joinTable->name;
    }

    public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform): array
    {
        return $class->identifier;
    }

    public function getColumnAlias(
        string $columnName,
        int $counter,
        AbstractPlatform $platform,
        ?ClassMetadata $class = null,
    ): string {
        return $this->getSQLResultCasing($platform, $columnName.'_'.$counter);
    }
}
