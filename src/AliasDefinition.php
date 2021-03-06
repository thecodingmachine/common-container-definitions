<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;
use Interop\Container\Compiler\InlineEntryInterface;

/**
 * This class represents an alias to another entry in the container.
 */
class AliasDefinition implements DefinitionInterface
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
     * Returns an InlineEntryInterface object representing the PHP code necessary to generate
     * the container entry.
     *
     * @param string $containerVariable The name of the variable that allows access to the container instance. For instance: "$container", or "$this->container"
     * @param array $usedVariables An array of variables that are already used and that should not be used when generating this code.
     * @return InlineEntryInterface
     */
    public function toPhpCode($containerVariable, array $usedVariables = array())
    {
        return new InlineEntry(sprintf('%s->get(%s)', $containerVariable, var_export($this->alias, true)), null, $usedVariables);
    }
}
