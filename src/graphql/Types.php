<?php

namespace Vertuoza\Api\Graphql;

use Closure;
use Exception;
use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\PhpEnumType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use ReflectionException;
use function count;
use function explode;
use function lcfirst;

final class Types
{
    /**
     * @var array<string, Type, NamedType>
     */
    private static array $types = [];

    /**
     * @param class-string $classname
     *
     * @return string
     */
    private static function normalizeClassName(string $classname): string
    {
        $parts = explode('\\', $classname);

        assert(is_string($parts[count($parts) - 1]), 'regex is statically known to be correct');

        return \strtolower($parts[count($parts) - 1]);
    }

    /**
     * @param class-string<Type, NamedType> $classname
     *
     * @return Closure(): Type
     */
    public static function get(string $classname): Closure
    {
        return static fn () => self::byClassName($classname);
    }

    /**
     * @param string $classname
     *
     * @return Type
     */
    private static function getEnum(string $classname): Type
    {
        return self::byEnumClassName($classname);
    }


    /**
     * @param class-string<Type, NamedType> $classname
     */
    private static function byClassName(string $classname): Type
    {
        $cacheName = self::normalizeClassName($classname);

        if (!isset(self::$types[$cacheName])) {
            self::$types[$cacheName] = new $classname();
        }

        return self::$types[$cacheName];
    }

    /**
     * @param string $classname
     *
     * @return Type
     *
     * @throws ReflectionException
     */
    private static function byEnumClassName(string $classname): Type
    {
        $cacheName = self::normalizeClassName($classname);

        if (!isset(self::$types[$cacheName])) {
            self::$types[$cacheName] = new PhpEnumType($classname);
        }

        return self::$types[$cacheName];
    }

    /**
     * @param string $shortName
     *
     * @return Type|NamedType|ScalarType
     *
     * @throws Exception
     */
    public static function byTypeName(string $shortName): Type|NamedType|ScalarType
    {
        $cacheName = self::normalizeClassName($shortName);

        if (isset(self::$types[$cacheName])) {
            return self::$types[$cacheName];
        }

        $method = lcfirst($shortName);
        switch ($method) {
            case 'boolean':
                return self::boolean();
            case 'float':
                return self::float();
            case 'id':
                return self::id();
            case 'int':
                return self::int();
            case 'string':
                return self::string();

            default:
                throw new Exception("Unknown graphql type: {$shortName}");
        }
    }

    /**
     * @return ScalarType
     *
     * @throws InvariantViolation
     */
    public static function boolean(): ScalarType
    {
        return Type::boolean();
    }

    /**
     * @return ScalarType
     *
     * @throws InvariantViolation
     */
    public static function float(): ScalarType
    {
        return Type::float();
    }

    /**
     * @return ScalarType
     *
     * @throws InvariantViolation
     */
    public static function id(): ScalarType
    {
        return Type::id();
    }

    /**
     * @return ScalarType
     *
     * @throws InvariantViolation
     */
    public static function int(): ScalarType
    {
        return Type::int();
    }

    /**
     * @return ScalarType
     *
     * @throws InvariantViolation
     */
    public static function string(): ScalarType
    {
        return Type::string();
    }
}
