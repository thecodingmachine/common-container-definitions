<?php
namespace Mouf\Container\Definition;

class AliasDefinitionTest extends AbstractDefinitionTest
{

    public function testGetters() {
        $parameterDefinition = new AliasDefinition("test", "alias");

        $this->assertEquals("test", $parameterDefinition->getIdentifier());
        $this->assertEquals("alias", $parameterDefinition->getAlias());
    }

    public function testSimpleAlias() {
        $instanceDefinition = new InstanceDefinition("test", "Mouf\\Container\\Definition\\Fixtures\\Test");
        $aliasDefinition = new AliasDefinition("alias", "test");

        $container = $this->getContainer([
            "test" => $instanceDefinition,
            "alias" => $aliasDefinition
        ]);
        $result = $container->get("test");
        $alias = $container->get("alias");

        $this->assertEquals($result, $alias);
    }
}
