<?php

lmb_require('limb/cron/src/lmbCronJobLogger.class.php');

class lmbCronJobLoggerTest extends UnitTestCase
{	
	/**
	 * @var lmbCronJobLogger
	 */	
	protected $logger;
	
	function setUp()
	{
		$this->logger = new lmbCronJobLogger();
		$this->cleanup();
	}
	
	function tearDown()
	{
		$this->cleanup();
	}
	
	function cleanup()
	{
		$conn = lmbToolkit::instance()->getDefaultDbConnection();
		$table = new lmbTableGateway($this->logger->table_name, $conn);
		$table->delete();
	} 
	
	function testGetRecord_empty()
	{    
    $this->assertEqual(0, count($this->logger->getRecords()));		
	}
	
	function testMakeStartRecord()
	{
		$this->logger->setJobName('foo');
		$this->logger->makeStartRecord('bar');
		
		$records = $this->logger->getRecords();
		if($this->assertEqual(1, count($records)))
		{
		  $record = $this->logger->getRecords()->at(0);
		  $this->assertEqual($record->get('name'), 'foo');
		  $this->assertEqual($record->get('info'), 'bar');
		  $this->assertEqual($record->get('status'), lmbCronJobLogger::STATUS_START);
		}
	}
	
  function testMakeConflictRecord()
  {
    $this->logger->setJobName('foo');
    $this->logger->makeConflictRecord('bar');
    
    $records = $this->logger->getRecords();
    if($this->assertEqual(1, count($records)))
    {
      $record = $this->logger->getRecords()->at(0);
      $this->assertEqual($record->get('name'), 'foo');
      $this->assertEqual($record->get('info'), 'bar');
      $this->assertEqual($record->get('status'), lmbCronJobLogger::STATUS_CONFLICT);
    }
  }
  
  function testMakeEndRecord()
  {
    $this->logger->makeEndRecord($error = 'some error');
    $this->logger->makeEndRecord(null);
    
    $records = $this->logger->getRecords();
    if($this->assertEqual(2, count($records)))
    {
      $record = $this->logger->getRecords()->at(1);
      $this->assertEqual($record->get('info'), $error);
      $this->assertEqual($record->get('status'), lmbCronJobLogger::STATUS_ERROR);
      
      $record = $this->logger->getRecords()->at(0);
      $this->assertEqual($record->get('status'), lmbCronJobLogger::STATUS_SUCCESS);
    }
  }
  
  function testMakeExceptionRecord()
  {
    $this->logger->makeExceptionRecord($info = 'some exception info');
    
    $records = $this->logger->getRecords();
    if($this->assertEqual(1, count($records)))
    {
      $record = $this->logger->getRecords()->at(0);
      $this->assertEqual($record->get('info'), $info);
      $this->assertEqual($record->get('status'), lmbCronJobLogger::STATUS_EXCEPTION);      
    }
  }
}