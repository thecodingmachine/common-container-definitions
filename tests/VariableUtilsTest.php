<?php
namespace Mouf\Container\Definition;

use Mouf\Container\Definition\Fixtures\Test;
use Mouf\Picotainer\Picotainer;

class VariableUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testGetNextAvailableVariableName() {

        $this->assertEquals('$a', VariableUtils::getNextAvailableVariableName('a', []));
        $this->assertEquals('$a', VariableUtils::getNextAvailableVariableName('$a', []));
        $this->assertEquals('$a1', VariableUtils::getNextAvailableVariableName('$a', ['$a']));
        $this->assertEquals('$a2', VariableUtils::getNextAvailableVariableName('$a', ['$a', '$a1']));
        $this->assertEquals('$a10', VariableUtils::getNextAvailableVariableName('$a', ['$a', '$a1', '$a2', '$a3', '$a4', '$a5', '$a6', '$a7', '$a8', '$a9']));

        $this->assertEquals('$a10', VariableUtils::getNextAvailableVariableName('10', []));
        $this->assertEquals('$b', VariableUtils::getNextAvailableVariableName('#${}b', []));
    }
}

