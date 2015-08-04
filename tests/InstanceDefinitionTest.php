<?php
namespace Mouf\Container\Definition;

use Mouf\Picotainer\Picotainer;

class InstanceDefinitionTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleEmptyConstructor() {
        $instanceDefinition = new InstanceDefinition("test", "\\stdClass");
        $phpCode = $instanceDefinition->toPhpCode();
        $closure = eval("return ".$phpCode.";");
        $picotainer = new Picotainer([]);
        $this->assertInstanceOf("\\Closure", $closure);
        $result = $closure($picotainer);

        $this->assertInstanceOf("\\stdClass", $result);
    }

    public function testSimpleConstructorWithArguments() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument(42);
        $instanceDefinition->addConstructorArgument([12, [24, 42]]);

        $phpCode = $instanceDefinition->toPhpCode();
        $closure = eval("return ".$phpCode.";");
        $picotainer = new Picotainer([]);
        $this->assertInstanceOf("\\Closure", $closure);
        $result = $closure($picotainer);

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals(42, $result->cArg1);
        $this->assertEquals([12, [24, 42]], $result->cArg2);
    }
}

