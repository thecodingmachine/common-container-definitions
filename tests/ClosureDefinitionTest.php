<?php
namespace Mouf\Container\Definition;

class ClosureDefinitionTest extends AbstractDefinitionTest
{

    public function testGetters() {
        $closure = function() { return "foo"; };
        $closureDefinition = new ClosureDefinition("test", $closure);

        $this->assertEquals("test", $closureDefinition->getIdentifier());
        $this->assertEquals($closure, $closureDefinition->getClosure());
    }

    public function testSimpleClosure() {
        $closure = function() { return "foo"; };
        $closureDefinition = new ClosureDefinition("test", $closure);

        $container = $this->getContainer([
            "test" => $closureDefinition
        ]);
        $result = $container->get("test");

        $this->assertEquals("foo", $result);
    }

    /**
     * @expectedException \Mouf\Container\Definition\DefinitionException
     */
    public function testFailOnThis() {
        $closure = function() { return $this->testGetters(); };
        $closureDefinition = new ClosureDefinition("test", $closure);

        $container = $this->getContainer([
            "test" => $closureDefinition
        ]);
        $container->get("test");
    }

    /**
     * @expectedException \Mouf\Container\Definition\DefinitionException
     */
    public function testFailOnUse() {
        $a = 42;
        $closure = function() use ($a) { return "foo"; };
        $closureDefinition = new ClosureDefinition("test", $closure);

        $container = $this->getContainer([
            "test" => $closureDefinition
        ]);
        $container->get("test");
    }

    public function testInlineClosureDefinition() {
        $closure = function() { return "foo"; };
        // null passed as first parameter. This will generate an inline declaration.
        $dependencyDefinition = new ClosureDefinition(null, $closure);

        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument($dependencyDefinition);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals("foo", $result->cArg1);
    }
}

