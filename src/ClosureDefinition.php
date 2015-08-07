<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;
use SuperClosure\Analyzer\TokenAnalyzer;

/**
 * This class represents a closure.
 * Important! The code of the closure will be COPIED, not referenced.
 */
class ClosureDefinition implements DefinitionInterface, ReferenceInterface, DumpableValueInterface
{

    /**
     * The identifier of the instance in the container.
     *
     * @var string
     */
    private $identifier;

    /**
     * The closure.
     *
     * @var \Closure
     */
    private $closure;

    /**
     * Constructs an instance definition.
     *
     * @param string|null $identifier The identifier of the entry in the container. Can be null if the entry is anonymous (declared inline in other instances)
     * @param \Closure $closure The closure. It should not contain context (i.e. no "use" keyword in the closure definition). It should accept one compulsory parameter: the container.
     */
    public function __construct($identifier, \Closure $closure)
    {
        $this->identifier = $identifier;
        $this->closure = $closure;
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
     * Returns the closure of the parameter.
     * @return mixed
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * @return string
     */
    public function toPhpCode()
    {
        $analyzer = new TokenAnalyzer();
        $analysis = $analyzer->analyze($this->closure);

        if ($analysis['hasThis']) {
            throw new DefinitionException('Your closure cannot call the $this keyword.');
        }
        if (!empty($analysis['context'])) {
            throw new DefinitionException('Your closure cannot have a context (i.e. cannot have a "use" keyword).');
        }
        return $analysis['code'];
    }

    /**
     * @return string
     */
    public function dumpCode()
    {
        $code = $this->toPhpCode();
        $variableName = VariableUtils::getNextVariableName();
        $assignClosure = sprintf("\$%s = %s;", $variableName, $code);
        return new DumpedValue("\$".$variableName.'($container)', $assignClosure);
    }
}
