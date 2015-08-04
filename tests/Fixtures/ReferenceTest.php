<?php
namespace Mouf\Container\Definition;

use Mouf\Container\Definition\Fixtures\Test;
use Mouf\Picotainer\Picotainer;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{

    public function testGetters() {
        $reference = new Reference("foo");
        $this->assertEquals("foo", $reference->getIdentifier());
    }
}

