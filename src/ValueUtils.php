<?php
namespace Mouf\Container\Definition;


class ValueUtils
{
    /**
     * Dumps values.
     *
     * @param mixed $value
     *
     * @return DumpedValue
     *
     * @throws \RuntimeException
     */
    public static function dumpValue($value)
    {
        if (is_array($value)) {
            $code = array();
            $prependCode = array();
            foreach ($value as $k => $v) {
                $key = self::dumpValue($k);
                $value = self::dumpValue($v);

                if ($key->getPrependCode()) {
                    $prependCode[] = $key->getPrependCode();
                }
                if ($value->getPrependCode()) {
                    $prependCode[] = $value->getPrependCode();
                }

                $code[] = sprintf('%s => %s', $key->getCode(), $value->getCode());
            }

            return new DumpedValue(sprintf('array(%s)', implode(', ', $code)), implode("\n", $prependCode));
        } elseif ($value instanceof InstanceDefinition) {
            return self::dumpInstanceDefinition($value);
        } elseif ($value instanceof DumpableValueInterface) {
            return new DumpedValue($value->dumpCode());
        } elseif (is_object($value) || is_resource($value)) {
            throw new \RuntimeException('Unable to dump a container if a parameter is an object or a resource.');
        } else {
            return new DumpedValue(var_export($value, true));
        }
    }

    public static function dumpArguments($argumentsValues) {
        $arguments = [];
        $prependedCode = [];
        foreach ($argumentsValues as $argument) {
            $dumpedValue = ValueUtils::dumpValue($argument);
            $arguments[] = $dumpedValue->getCode();
            if (!empty($dumpedValue->getPrependCode())) {
                $prependedCode[] = $dumpedValue->getPrependCode();
            }
        }
        $argumentsCode = implode(', ', $arguments);
        $prependedCodeString = implode("\n", $prependedCode);
        return new DumpedValue($argumentsCode, $prependedCodeString);
    }

    private static function dumpInstanceDefinition(InstanceDefinition $definition) {
        // If the identifier is null, we must inline the definition.
        if ($definition->getIdentifier() === null) {
            $variableName = VariableUtils::getNextVariableName();
            $code = $definition->toInlinePhpCode($variableName);
            return new DumpedValue("\$".$variableName, $code);
        }

        $reference = new Reference($definition->getIdentifier());
        return self::dumpValue($reference);
    }
}
