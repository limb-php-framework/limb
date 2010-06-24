<?php

class lmbLoadedHandleClass
{
  var $test_var;

  function __construct($value = 'default')
  {
    $this->test_var = $value;
  }

  function bar()
  {
    return 'bar';
  }
}


