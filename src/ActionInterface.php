<?php
namespace Mouf\Container\Definition;

/**
 * Classes implementing ActionInterface represent a line of PHP code that is an action performed on an object.
 * This can be a method call or a public property assignement.
 */
interface ActionInterface
{

    /**
     * Generates PHP code for the line.
     * @param string $variableName
     * @return mixed
     */
    public function toPhpCode($variableName);
}
