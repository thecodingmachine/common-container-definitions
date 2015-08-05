<?php
namespace Mouf\Container\Definition;

class ParameterDefinitionTest extends \PHPUnit_Framework_TestCase
{

    public function testGetters() {
        $parameterDefinition = new ParameterDefinition("test", "value");

        $this->assertEquals("test", $parameterDefinition->getIdentifier());
        $this->assertEquals("value", $parameterDefinition->getValue());
    }

    public function testSimpleEncode() {
        $parameterDefinition = new ParameterDefinition("test", "value");
        $this->assertEquals("'value'", $parameterDefinition->toPhpCode());
    }
}
