<?php
namespace Mouf\Container\Definition;


use Interop\Container\Compiler\DefinitionInterface;

class ValueUtils
{
    /**
     * Dumps values.
     *
     * @param mixed $value
     * @param string $containerVariable
     * @param array $usedVariables
     * @return InlineEntry
     */
    public static function dumpValue($value, $containerVariable, array $usedVariables)
    {
        if (is_array($value)) {
            return self::dumpArray($value, $containerVariable, $usedVariables);
        } elseif ($value instanceof DefinitionInterface) {
            return self::dumpDefinition($value, $containerVariable, $usedVariables);
        } elseif (is_object($value) || is_resource($value)) {
            throw new \RuntimeException('Unable to dump a container if a parameter is an object or a resource.');
        } else {
            return new InlineEntry(var_export($value, true), null, $usedVariables);
        }
    }

    public static function dumpArguments($argumentsValues, $containerVariable, array $usedVariables) {
        $arguments = [];
        $prependedCode = [];
        foreach ($argumentsValues as $argument) {
            $inlineEntry = ValueUtils::dumpValue($argument, $containerVariable, $usedVariables);
            $usedVariables = $inlineEntry->getUsedVariables();
            $arguments[] = $inlineEntry->getExpression();
            if (!empty($inlineEntry->getStatements())) {
                $prependedCode[] = $inlineEntry->getStatements();
            }
        }
        $argumentsCode = implode(', ', $arguments);
        $prependedCodeString = implode("\n", $prependedCode);
        return new InlineEntry($argumentsCode, $prependedCodeString, $usedVariables);
    }

    private static function dumpArray(array $value, $containerVariable, array $usedVariables) {
        $code = array();
        $prependCode = array();
        foreach ($value as $k => $v) {
            $value = self::dumpValue($v, $containerVariable, $usedVariables);

            if ($value->getStatements()) {
                $prependCode[] = $value->getStatements();
            }
            $usedVariables = $value->getUsedVariables();

            $code[] = sprintf('%s => %s', var_export($k, true), $value->getExpression());
        }

        return new InlineEntry(sprintf('array(%s)', implode(', ', $code)), implode("\n", $prependCode), $usedVariables);
    }

    private static function dumpDefinition(DefinitionInterface $definition, $containerVariable, array $usedVariables) {
        // If the identifier is null, we must inline the definition.
        if ($definition->getIdentifier() === null) {
            return $definition->toPhpCode($containerVariable, $usedVariables);
        } else {
            return new InlineEntry(sprintf("%s->get(%s)", $containerVariable, var_export($definition->getIdentifier(), true)), null, $usedVariables);
        }
    }
}
