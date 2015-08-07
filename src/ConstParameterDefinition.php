<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;

/**
 * This class represents a constant parameter (a "define" or a const in a class).
 */
class ConstParameterDefinition implements DefinitionInterface, ReferenceInterface, DumpableValueInterface
{

    /**
     * The identifier of the instance in the container.
     *
     * @var string
     */
    private $identifier;

    /**
     * The name of the constant. If it is a class constant, please pass the FQDN. For instance: "My\Class::CONSTANT"
     *
     * @var string
     */
    private $const;

    /**
     * Constructs an instance definition.
     *
     * @param string|null $identifier The identifier of the entry in the container. Can be null if the entry is anonymous (declared inline in other instances)
     * @param string $const The name of the constant. If it is a class constant, please pass the FQDN. For instance: "My\Class::CONSTANT"
     */
    public function __construct($identifier, $const)
    {
        $this->identifier = $identifier;
        $this->const = $const;
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
     * The name of the constant. If it is a class constant, please pass the FQDN. For instance: "My\Class::CONSTANT"
     * @return mixed
     */
    public function getConst()
    {
        return $this->const;
    }

    /**
     * @return string
     */
    public function toPhpCode()
    {
        return $this->const;
    }

    /**
     * @return string
     */
    public function dumpCode()
    {
        return new DumpedValue($this->toPhpCode());
    }
}
