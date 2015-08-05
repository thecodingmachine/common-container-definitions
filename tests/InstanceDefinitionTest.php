<?php
namespace Mouf\Container\Definition;

use Mouf\Container\Definition\Fixtures\Test;
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

    /**
     * @expectedException \RuntimeException
     */
    public function testSimpleConstructorWithException() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument(new Test());

        $instanceDefinition->toPhpCode();
    }

    public function testMethodCall() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addMethodCall("setArg1")->addArgument(42);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals("42", $result->cArg1);
    }

    public function testPropertyAssignment() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->setProperty("cArg1", 42);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals("42", $result->cArg1);
    }

    public function testInlineDeclaration() {
        // null passed as first parameter. This will generate an inline declaration.
        $dependencyDefinition = new InstanceDefinition(null, "Mouf\\Container\\Definition\\Fixtures\\Test");
        $dependencyDefinition->addConstructorArgument("hello");

        $dependencyDefinition2 = new InstanceDefinition(null, "Mouf\\Container\\Definition\\Fixtures\\Test");
        $dependencyDefinition2->addConstructorArgument("hello2");


        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument($dependencyDefinition);
        $instanceDefinition->addConstructorArgument([$dependencyDefinition2]);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result->cArg1);
        $this->assertEquals("hello", $result->cArg1->cArg1);
        $this->assertEquals("hello2", $result->cArg2[0]->cArg1);
    }

    public function testInlineParameterDeclaration() {
        // null passed as first parameter. This will generate an inline declaration.
        $dependencyDefinition = new ParameterDefinition(null, "hello");

        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument($dependencyDefinition);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals("hello", $result->cArg1);
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

