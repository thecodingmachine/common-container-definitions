<?php
namespace Mouf\Container\Definition\Fixtures;


class Test
{

    public $cArg1;
    public $cArg2;

    public function __construct($cArg1 = null, $cArg2 = null) {
        $this->cArg1 = $cArg1;
        $this->cArg2 = $cArg2;
    }
}
