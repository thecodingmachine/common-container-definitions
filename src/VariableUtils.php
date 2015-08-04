<?php
namespace Mouf\Container\Definition;


class VariableUtils
{
    /**
     * Characters that might appear in the generated variable name as first character.
     *
     * @var string
     */
    const FIRST_CHARS = 'abcdefghijklmnopqrstuvwxyz';

    /**
     * Characters that might appear in the generated variable name as any but the first character.
     *
     * @var string
     */
    const NON_FIRST_CHARS = 'abcdefghijklmnopqrstuvwxyz0123456789_';

    private static $variableCount = 0;
    private static $loopLevel = 0;

    /**
     * Increses the loop counter
     */
    public static function enter() {
        self::$loopLevel++;
    }

    /**
     * Decreases the loop counter
     * When counter reaches 0, variable names are reset.
     */
    public static function leave() {
        self::$loopLevel--;
        if (self::$loopLevel <= 0) {
            self::$variableCount = 0;
        }
    }

    /**
     * Returns the next name to use.
     * Borrowed from Symfony, thanks guys :)
     *
     * @return string
     */
    public static function getNextVariableName()
    {
        $firstChars = self::FIRST_CHARS;
        $firstCharsLength = strlen($firstChars);
        $nonFirstChars = self::NON_FIRST_CHARS;
        $nonFirstCharsLength = strlen($nonFirstChars);

        while (true) {
            $name = '';
            $i = self::$variableCount;

            if ('' === $name) {
                $name .= $firstChars[$i % $firstCharsLength];
                $i = (int) ($i / $firstCharsLength);
            }

            while ($i > 0) {
                --$i;
                $name .= $nonFirstChars[$i % $nonFirstCharsLength];
                $i = (int) ($i / $nonFirstCharsLength);
            }

            ++self::$variableCount;

            // check that the name is not reserved
            if (in_array($name, ['container', 'instance'], true)) {
                continue;
            }

            return $name;
        }
    }
}
