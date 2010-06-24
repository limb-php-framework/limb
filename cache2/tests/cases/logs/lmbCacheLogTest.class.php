<?php

lmb_require('limb/cache2/src/lmbCache.interface.php');

abstract class lmbCacheLogTest extends UnitTestCase
{
  /**
   * @var lmbCacheLog
   */
  protected $logger;
  /**
   *@todo
   */
  function testAddRecord_GetRecords()
  {
    $this->logger->addRecord($key1 = 'foo1', $operation1 = lmbCache::OPERATION_ADD, $time1 = 42, $result1 = true);
    $this->logger->addRecord($key2 = 'foo2', $operation2 = lmbCache::OPERATION_DECREMENT, $time2 = 43, $result2 = false);

    $records = $this->logger->getRecords();

    $this->assertEqual(2, count($records));

    $this->assertIdentical($records[0], array(
      'key' => $key1,
      'operation' => $operation1,
      'time' => $time1,
      'result' => $result1
    ));

    $this->assertIdentical($records[1], array(
      'key' => $key2,
      'operation' => $operation2,
      'time' => $time2,
      'result' => $result2
    ));
  }
  /**
   *@todo
   */
  function testGetStatistic(){}

}
