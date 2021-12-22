<?php

namespace Zfegg\CallableHandlerDecorator\Utils;


/**
 * Sample `Doctrine\Inflector\Inflector`
 * @internal
 * @see https://github.com/doctrine/Inflector
 */
class Word
{
    /**
     * Converts a word into the format for a Doctrine table name. Converts 'ModelName' to 'model_name'.
     */
    public static function tableize(string $word, string $separator = '_'): string
    {
        $tableized = preg_replace('~(?<=\\w)([A-Z])~u', $separator . '$1', $word);

        return strtolower($tableized);
    }

    /**
     * Converts a word into the format for a Doctrine class name. Converts 'table_name' to 'TableName'.
     */
    public static function classify(string $word): string
    {
        return str_replace([' ', '_', '-'], '', ucwords($word, ' _-'));
    }

    /**
     * Camelizes a word. This uses the classify() method and turns the first character to lowercase.
     */
    public static function camelize(string $word): string
    {
        return lcfirst(self::classify($word));
    }
}