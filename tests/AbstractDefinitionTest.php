<?php
namespace Mouf\Container\Definition;

use Mouf\Picotainer\Picotainer;

abstract class AbstractDefinitionTest extends \PHPUnit_Framework_TestCase
{
    protected function getContainer(array $definitions) {
        $closures = [];
        foreach ($definitions as $key => $definition) {
            $closures[$key] = eval("return ".$definition->toPhpCode().";");
        }
        $picotainer = new Picotainer($closures);
        return $picotainer;
    }
}
