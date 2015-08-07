<?php
namespace Mouf\Container\Definition;


/**
 * A class representing objects that can be generated as PHP code.
 */
interface DumpableValueInterface
{
    /**
     * @return DumpedValue
     */
    public function dumpCode();
}