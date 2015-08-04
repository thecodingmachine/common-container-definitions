<?php
namespace Mouf\Container\Definition\Fixtures;


class Test
{

    public $cArg1;
    public $cArg2;

    public function __construct($cArg1, $cArg2) {
        $this->cArg1 = $cArg1;
        $this->cArg2 = $cArg2;
    }
}
