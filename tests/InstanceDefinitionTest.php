<?php
namespace Mouf\Container\Definition;

use Mouf\Picotainer\Picotainer;

class InstanceDefinitionTest extends \PHPUnit_Framework_TestCase
{

    public function testGetters() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument(42);
        $instanceDefinition->addConstructorArgument([12, [24, 42]]);

        $this->assertEquals("Mouf\\Container\\Definition\\Fixtures\\Test", $instanceDefinition->getClassName());
        $this->assertEquals("test", $instanceDefinition->getIdentifier());
        $this->assertCount(2, $instanceDefinition->getConstructorParameters());
    }

    public function testSimpleEmptyConstructor() {
        $instanceDefinition = new InstanceDefinition("test", "\\stdClass");

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("\\stdClass", $result);
    }

    public function testSimpleConstructorWithArguments() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument(42);
        $instanceDefinition->addConstructorArgument([12, [24, 42]]);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals(42, $result->cArg1);
        $this->assertEquals([12, [24, 42]], $result->cArg2);
    }

    public function testSimpleConstructorWithReferenceArguments() {
        $dependencyDefinition = new InstanceDefinition("dependency", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $dependencyDefinition->addConstructorArgument("hello");

        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument($dependencyDefinition);

        $container = $this->getContainer([
            "dependency" => $dependencyDefinition,
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result->cArg1);
        $this->assertEquals("hello", $result->cArg1->cArg1);
    }

    private function getContainer(array $definitions) {
        $closures = [];
        foreach ($definitions as $key => $definition) {
            $closures[$key] = eval("return ".$definition->toPhpCode().";");
        }
        $picotainer = new Picotainer($closures);
        return $picotainer;
    }
}

