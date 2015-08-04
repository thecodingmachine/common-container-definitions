<?php


namespace Mouf\Container\Definition;

/**
 * Represents an assignement of a property
 */
class PropertyAssignement implements ActionInterface
{
    /**
     * The name of the property
     *
     * @var string
     */
    private $propertyName;

    /**
     * The value to assign to the property.
     *
     * @var mixed
     */
    private $value;

    /**
     * @param string $propertyName
     * @param mixed $value
     */
    public function __construct($propertyName, $value)
    {
        $this->propertyName = $propertyName;
        $this->value = $value;
    }

    /**
     * Generates PHP code for the line.
     * @param string $variableName Variable name without the $
     * @return mixed
     */
    public function toPhpCode($variableName)
    {
        $dumpedValue = ValueUtils::dumpValue($this->value);
        return $dumpedValue->getPrependCode().sprintf("$%s->%s = %s;", $variableName, $this->propertyName, $dumpedValue->getCode());
    }
}
