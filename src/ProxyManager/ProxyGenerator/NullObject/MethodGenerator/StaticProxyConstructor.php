<?php

declare(strict_types=1);

namespace ProxyManager\ProxyGenerator\NullObject\MethodGenerator;

use ProxyManager\Generator\MethodGenerator;
use ProxyManager\ProxyGenerator\Util\Properties;
use ReflectionClass;
use ReflectionProperty;
use Zend\Code\Generator\Exception\InvalidArgumentException;
use function array_map;
use function implode;

/**
 * The `staticProxyConstructor` implementation for null object proxies
 *
 */
class StaticProxyConstructor extends MethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass $originalClass Reflection of the class to proxy
     *
     * @throws InvalidArgumentException
     */
    public function __construct(ReflectionClass $originalClass)
    {
        parent::__construct('staticProxyConstructor', [], static::FLAG_PUBLIC | static::FLAG_STATIC);

        $nullableProperties = array_map(
            function (ReflectionProperty $publicProperty) : string {
                return '$instance->' . $publicProperty->getName() . ' = null;';
            },
            Properties::fromReflectionClass($originalClass)->getPublicProperties()
        );

        $this->setReturnType($originalClass->getName());
        $this->setDocBlock('Constructor for null object initialization');
        $this->setBody(
            'static $reflection;' . "\n\n"
            . '$reflection = $reflection ?? $reflection = new \ReflectionClass(__CLASS__);' . "\n"
            . '$instance = $reflection->newInstanceWithoutConstructor();' . "\n\n"
            . ($nullableProperties ? implode("\n", $nullableProperties) . "\n\n" : '')
            . 'return $instance;'
        );
    }
}
