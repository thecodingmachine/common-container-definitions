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
     * @param array $constructorArguments A list of constructor arguments.
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

    /**
     * Adds an argument to the list of arguments to be passed to the constructor.
     * @param mixed $argument
     * @return self
     */
    public function addConstructorArgument($argument) {
        $this->constructorArguments[] = $argument;
        return $this;
    }

    /**
     * Adds a method call.
     *
     * @param string $methodName
     * @param array $arguments
     * @return MethodCall
     */
    public function addMethodCall($methodName, array $arguments = array()) {
        $this->actions[] = $methodCall = new MethodCall($methodName, $arguments);
        return $methodCall;
    }

    /**
     * Adds a method call.
     *
     * @param string $propertyName
     * @param mixed $value
     * @return self
     */
    public function setProperty($propertyName, $value) {
        $this->actions[] = new PropertyAssignement($propertyName, $value);
        return $this;
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
        try {
            VariableUtils::enter();
            $code = $this->toInlinePhpCode("instance");

            $code .= "return \$instance;";
            return self::wrapInFunction($code);
        } finally {
            VariableUtils::leave();
        }
    }

    /**
     * Generates PHP code for inline declaration of this instance.
     * @param $variableName
     */
    public function toInlinePhpCode($variableName) {
        $arguments = [];
        $prependedCode = [];
        foreach ($this->constructorArguments as $argument) {
            $dumpedValue = ValueUtils::dumpValue($argument);
            $arguments[] = $dumpedValue->getCode();
            if (!empty($dumpedValue->getPrependCode())) {
                $prependedCode[] = $dumpedValue->getPrependCode();
            }
        }
        $argumentsCode = implode(', ', $arguments);
        $prependedCodeString = implode("\n", $prependedCode);
        $newStatement = sprintf("new %s(%s)", $this->className, $argumentsCode);
        $code = sprintf("\$%s = %s;\n", $variableName, $newStatement);
        foreach ($this->actions as $action) {
            $code .= $action->toPhpCode($variableName)."\n";
        }
        return $prependedCodeString.$code;
    }

    private static function wrapInFunction($str) {
        return sprintf('function(Interop\\Container\\ContainerInterface $container) {
            %s
        }', $str);
    }
}
