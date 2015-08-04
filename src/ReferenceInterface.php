<?php
namespace Mouf\Container\Definition;


interface ReferenceInterface
{
    /**
     * Returns the identifier of the instance.
     * @return string
     */
    public function getIdentifier();
}
