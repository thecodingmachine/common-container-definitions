<?php
namespace Mouf\Container\Definition;

class ConstParameterDefinitionTest extends AbstractDefinitionTest
{
    const TEST_CONST = "const";

    public function testGetters() {
        $parameterDefinition = new ConstParameterDefinition("test", "value");

        $this->assertEquals("test", $parameterDefinition->getIdentifier());
        $this->assertEquals("value", $parameterDefinition->getConst());
    }

    public function testInlineConstDeclaration() {
        // null passed as first parameter. This will generate an inline declaration.
        $dependencyDefinition = new ConstParameterDefinition(null, "Mouf\\Container\\Definition\\ConstParameterDefinitionTest::TEST_CONST");

        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $instanceDefinition->addConstructorArgument($dependencyDefinition);

        $container = $this->getContainer([
            "test" => $instanceDefinition
        ]);
        $result = $container->get("test");

        $this->assertInstanceOf("Mouf\\Container\\Definition\\Fixtures\\Test", $result);
        $this->assertEquals("const", $result->cArg1);
    }
}
