<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;

/**
 * This class represents an alias to another entry in the container.
 */
class AliasDefinition extends AbstractDefinition implements DefinitionInterface, ReferenceInterface, DumpableValueInterface
{

    /**
     * The identifier of the entry in the container.
     *
     * @var string
     */
    private $identifier;

    /**
     * The identifier of the entry we are aliasing.
     *
     * @var string
     */
    private $alias;

    /**
     * Constructs an instance definition.
     *
     * @param string|null $identifier The identifier of the entry in the container. Can be null if the entry is anonymous (declared inline in other instances)
     * @param string $alias The identifier of the entry we are aliasing.
     */
    public function __construct($identifier, $alias)
    {
        $this->identifier = $identifier;
        $this->alias = $alias;
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
     * The identifier of the entry we are aliasing.
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function toPhpCode()
    {
        return self::wrapInFunction("return ".$this->dumpCode()->getCode().";");
    }

    /**
     * @return string
     */
    public function dumpCode()
    {
        return new DumpedValue('$container->get('.var_export($this->alias, true).')');
    }
}
