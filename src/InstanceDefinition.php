<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;

/**
 * This class represents an instance declared using the "new" keyword followed by an optional list of
 * method calls and properties assignations.
 */
class InstanceDefinition implements DefinitionInterface, ReferenceInterface
{
    /**
     * The identifier of the instance in the container.
     *
     * @var string
     */
    private $identifier;

    /**
     * The fully qualified class name of this instance.
     *
     * @var string
     */
    private $className;

    /**
     * A list of arguments passed to the constructor.
     *
     * @var array Array of scalars or ReferenceInterface, or array mixing scalars, arrays, and ReferenceInterface
     */
    private $constructorArguments = array();

    /**
     * A list of actions to be executed (can be either a method call or a public property assignation)
     *
     * @var ActionInterface[]
     */
    private $actions = array();

    /**
     * Constructs an instance definition.
     *
     * @param string|null $identifier The identifier of the instance in the container. Can be null if the instance is anonymous (declared inline of other instances)
     * @param string $className The fully qualified class name of this instance.
     * @param array $constructorArguments A list of constructor parameters.
     */
    public function __construct($identifier, $className, array $constructorArguments = array())
    {
        $this->identifier = $identifier;
        $this->className = $className;
        $this->constructorArguments = $constructorArguments;
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
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getConstructorParameters()
    {
        return $this->constructorArguments;
    }

    public function addConstructorArgument($argument) {
        $this->constructorArguments[] = $argument;
    }

    /**
     * Returns a string of PHP code generating the container entry.
     *
     * The PHP code MUST be a closure, and that closure MUST take one argument that is a
     * Interop\Container\ContainerInterface object.
     * The function MUST return the entry generated.
     *
     * For instance, this is a valid PHP string:
     *
     * function(Interop\Container\ContainerInterface $container) {
     *     $service = new MyService($container->get('my_dependency'));
     *     return $service;
     * }
     *
     * @return string
     */
    public function toPhpCode()
    {
        $arguments = implode(', ', array_map([$this, "dumpValue"], $this->constructorArguments));
        $newStatement = sprintf("new %s(%s)", $this->className, $arguments);
        return sprintf('function(Interop\\Container\\ContainerInterface $container) {
            return %s;
        }', $newStatement);
    }

    /**
     * Dumps values.
     *
     * @param mixed $value
     * @param bool  $interpolate
     *
     * @return string
     *
     * @throws RuntimeException
     */
    private function dumpValue($value)
    {
        if (is_array($value)) {
            $code = array();
            foreach ($value as $k => $v) {
                $code[] = sprintf('%s => %s', $this->dumpValue($k), $this->dumpValue($v));
            }

            return sprintf('array(%s)', implode(', ', $code));
        } elseif ($value instanceof InstanceDefinition) {
            // TODO: this can also be a "Variable" if we inline definitions!
            $reference = new Reference($value);
            return $this->dumpValue($reference);
        } elseif ($value instanceof DumpableValueInterface) {
            return $value->dumpCode();
        } elseif (is_object($value) || is_resource($value)) {
            throw new \RuntimeException('Unable to dump a container if a parameter is an object or a resource.');
        } else {
            return var_export($value, true);
        }
    }

}
