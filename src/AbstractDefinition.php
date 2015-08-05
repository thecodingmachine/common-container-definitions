<?php


namespace Mouf\Container\Definition;


abstract class AbstractDefinition
{
    protected static function wrapInFunction($str) {
        return sprintf('function(\\Interop\\Container\\ContainerInterface $container) {
            %s
        }', $str);
    }
}