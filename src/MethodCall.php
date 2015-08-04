<?php


namespace Mouf\Container\Definition;

/**
 * Represents a call to a method.
 */
class MethodCall implements ActionInterface
{
    /**
     * The name of the method
     *
     * @var string
     */
    private $methodName;

    /**
     * A list of arguments passed to the constructor.
     *
     * @var array Array of scalars or ReferenceInterface, or array mixing scalars, arrays, and ReferenceInterface
     */
    private $arguments = array();

    /**
     * MethodCall constructor.
     * @param string $methodName
     * @param array $arguments
     */
    public function __construct($methodName, array $arguments = array())
    {
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }

    /**
     * Adds an argument to the list of arguments to be passed to the method.
     * @param mixed $argument
     * @return self
     */
    public function addArgument($argument) {
        $this->arguments[] = $argument;
        return $this;
    }


    /**
     * Generates PHP code for the line.
     * @param string $variableName Variable name without the $
     * @return mixed
     */
    public function toPhpCode($variableName)
    {
        $arguments = [];
        $prependedCode = [];
        foreach ($this->arguments as $argument) {
            $dumpedValue = ValueUtils::dumpValue($argument);
            $arguments[] = $dumpedValue->getCode();
            if (empty($dumpedValue->getPrependCode())) {
                $prependedCode[] = $dumpedValue->getPrependCode();
            }
        }
        $argumentsCode = implode(', ', $arguments);
        $prependedCodeString = implode("\n", $prependedCode);

        return $prependedCodeString.sprintf("$%s->%s(%s);", $variableName, $this->methodName, $argumentsCode);
    }
}
