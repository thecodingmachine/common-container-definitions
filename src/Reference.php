<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 03/08/15
 * Time: 19:08
 */

namespace Mouf\Container\Definition;

/**
 * A class representing a pointer to an instance.
 */
class Reference implements ReferenceInterface, DumpableValueInterface
{

    private $reference;

    /**
     * @param string $reference
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    public function dumpCode() {
        return '$container->get('.var_export($this->reference, true).')';
    }

    /**
     * Returns the identifier of the instance.
     * @return string
     */
    public function getIdentifier()
    {
        return $this->reference;
    }
}