<?php

class lmbPHPUnitTestCase extends UnitTestCase
{
  function assertEquals($first, $second, $message = '%s')
  {
    return $this->assertEqual($first, $second, $message);
  }

  function assertRegexp($pattern, $subject, $message = '%s')
  {
    return $this->assertPattern($pattern, $subject, $message);
  }

  function fail($message = '%s')
  {
    return parent::fail($message);
  }
}
