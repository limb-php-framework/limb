<?php

lmb_require('limb/cron/src/lmbCronBaseJob.class.php');

class ConcreteCronJob extends lmbCronBaseJob
{
	function run() {}
}

class lmbCronBaseJobTest extends UnitTestCase
{
	/**
	 * @var ConcreteCronJob
	 */
	protected $job;
	
	function setUp()
	{
		$this->cleanUp();
		$this->job = new ConcreteCronJob();
	}
	
	function tearDown()
	{
		$this->cleanUp();
	}
	
	function cleanUp()
	{
		lmbFs::rm(lmb_var_dir());
		lmbFs::mkdir(lmb_var_dir());
	}
	
	function testGetLockDir()
	{
		$this->assertEqual($this->job->getLocksDir(), lmb_var_dir().'/cron_job_locks');
	}
	
  function testSetLockDir()
  {  	
  	$this->job->setLocksDir($locks_dir = lmb_var_dir().'/foo');
  	
    $this->assertEqual($this->job->getLocksDir(), $locks_dir);
  }
  
  function testGetName()
  {
  	$this->assertEqual($this->job->getName(), 'ConcreteCronJob');
  }
  
  protected function _lockFileFromAnotherProcess($file_name)
  {
  	$output = '';
    exec('php -r "\$fp = fopen(\''.$file_name.'\', \'w\'); echo (int) flock(\$fp, LOCK_EX + LOCK_NB);"', $output);
    return (bool) $output[0];
  } 
  
  function testLock()
  {
  	$this->assertTrue($this->job->lock());
  	$this->assertTrue($this->job->lock());
  	  	
  	$this->assertTrue($this->_lockFileFromAnotherProcess($this->job->getLockFile().'foo'));
  	$this->assertFalse($this->_lockFileFromAnotherProcess($this->job->getLockFile()));
  }
  
  function testUnlock()
  {
  	$this->assertTrue($this->job->lock());
  	$this->job->unlock();
  	
  	$this->assertTrue($this->_lockFileFromAnotherProcess($this->job->getLockFile()));
  	$this->assertTrue($this->job->lock());
  }
}