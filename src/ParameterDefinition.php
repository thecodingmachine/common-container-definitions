<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;

/**
 * This class represents a parameter.
 */
class ParameterDefinition implements DefinitionInterface, ReferenceInterface, DumpableValueInterface
{

    /**
     * The identifier of the instance in the container.
     *
     * @var string
     */
    private $identifier;

    /**
     * The value of the parameter.
     * It is expected to be a scalar or an array (or more generally anything that can be `var_export`ed)
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructs an instance definition.
     *
     * @param string|null $identifier The identifier of the entry in the container. Can be null if the entry is anonymous (declared inline in other instances)
     * @param string $value The value of the parameter. It is expected to be a scalar or an array (or more generally anything that can be `var_export`ed)
     */
    public function __construct($identifier, $value)
    {
        $this->identifier = $identifier;
        $this->value = $value;
    }

    /**
     * Returns the identifier of the instance.
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the value of the parameter.
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toPhpCode()
    {
        return var_export($this->value, true);
    }

    /**
     * @return string
     */
    public function dumpCode()
    {
        return new DumpedValue($this->toPhpCode());
    }
}
