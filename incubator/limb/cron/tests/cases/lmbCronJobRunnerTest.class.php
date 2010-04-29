<?php

lmb_require('limb/cron/src/lmbCronJobRunner.class.php');
lmb_require('limb/cron/src/lmbCronJobLogger.class.php');

class lmbCronJobRunnerTest extends UnitTestCase
{
	/**
	 * @var lmbCronJobLogger
	 */
	protected $logger;
	/**
	 * @var lmbCronJobRunner
	 */
	protected $runner;	
	
	function setUp()
	{
		$this->logger = new lmbCronJobLogger();
		$table = new lmbTableGateway($this->logger->table_name);
		$table->delete();

		$job_class_content = 'function run() {  }';
		$this->runner = $this->_createRunner($job_class_content);
	}
	
	protected function _createRunner($job_class_content)
	{	
		$class_name = 'TestJob'.rand();
    $job_file_content =<<<EOD
<?php
class $class_name extends lmbCronBaseJob
{
  $job_class_content
} 
EOD;

    $job_file = lmb_var_dir().'/'.$class_name.'.class.php';
    file_put_contents($job_file, $job_file_content);    
    return new lmbCronJobRunner($this->logger, $job_file);
	}
	
	function testProcessError()
	{
		$this->runner->setDebugMode(false);
		$this->runner->processError(1, 'foo', 'bar', 2);		
		$this->assertEqual(
		  lmbCronJobLogger::STATUS_ERROR,
		  $this->logger->getRecords()->at(0)->get('status')
		);
		
		$this->runner->setDebugMode(false);
		$this->runner->processError(1, 'foo', 'bar', 2);
		$info = $this->logger->getRecords()->at(1)->get('info');
		$this->assertPattern('/1/', $info);
		$this->assertPattern('/foo/', $info);
		$this->assertPattern('/bar/', $info);
		$this->assertPattern('/2/', $info);
	}
	
  function testRun_start_and_end_records()
  {
    $this->runner->run();
    
    $this->assertEqual(0, count($this->logger->getRecords()));    

    $this->runner->fullLogMode(true);
    $this->runner->run();
    $this->assertEqual(
      lmbCronJobLogger::STATUS_START,
      $this->logger->getRecords()->at(1)->get('status')
    );
    $this->assertEqual(
      lmbCronJobLogger::STATUS_SUCCESS,
      $this->logger->getRecords()->at(0)->get('status')
    );
  }
  
  function testRun_check_job_run()
  {
  	$job_file_content =<<<EOD
  public \$runned = false;
  function run() { \$this->runned = true; }   
EOD;
    $runner = $this->_createRunner($job_file_content);             
    $this->assertFalse($runner->job->runned);
    
    $runner->run();
    $this->assertTrue($runner->job->runned);
  }
  
  function testGetFile()
  {
    $lock_file = $this->runner->getLockFile();
    $this->assertPattern('/TestJob/', $lock_file);    
  }
  
  protected function _tryLockFromAnotherProcess()
  {
  	$lock_file = $this->runner->getLockFile();
    $command = 'php -r "exit(flock(fopen(\''.$lock_file.'\', \'w\'), LOCK_EX + LOCK_NB));"';
    return (bool) exec($command);
  }
  
  function testLockJob()
  {    	  	  	  	
  	$this->assertTrue($this->_tryLockFromAnotherProcess());
  	
  	$this->assertTrue($this->runner->lockJob());  	
    $this->assertFalse($this->_tryLockFromAnotherProcess());
  }
  
  function testUnlockJob()
  {
    $this->runner->lockJob();
    $this->runner->unlockJob();    
    $this->assertTrue($this->_tryLockFromAnotherProcess());
  }
}
