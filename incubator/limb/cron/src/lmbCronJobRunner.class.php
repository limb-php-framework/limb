<?php

lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/cron/src/lmbCronBaseJob.class.php');

class lmbCronJobRunner
{
	/**
	 * @var lmbCronBaseJob
	 */
	public $job;
	/** 
   * @var lmbCronJobLogger
   */
  protected $logger;
  protected $debug_mode = false;
  protected $full_log_enabled = false;
  protected $lock_file_pointer;	
	
	function __construct($logger, $cron_job_file)
	{				
		$job_class = basename($cron_job_file);
		$job_class_name_end = strpos($job_class, '.');
		$job_class = substr($job_class, 0, $job_class_name_end);
		
		set_error_handler(array($this, 'processError'));
		
		lmb_require($cron_job_file);
		$this->job = new $job_class;

		$this->logger = $logger;
		$this->logger->setJobName($this->job->getName());				
	}
	
	function getLockFile()
	{		
		$lock_dir = lmb_var_dir().'/cron_locks/';
    if(!file_exists($lock_dir))
      lmbFs::mkdir($lock_dir);
		
		return $lock_dir.$this->job->getName().'.pid';
	}
	
	function lockJob()
	{ 
		$this->lock_file_pointer = fopen($this->getLockFile(), 'w');
    if(!flock($this->lock_file_pointer, LOCK_EX + LOCK_NB))
      return false;

    fwrite($this->lock_file_pointer, getmypid());    
    return true;
	}
	
	function unlockJob()
  { 
    flock($this->lock_file_pointer, LOCK_UN);
    fclose($this->lock_file_pointer);
  }
	
  function processError($errno, $errstr, $errfile, $errline)
  {
    $back_trace = new lmbBacktrace(10, 10);
    $error_str = "
      error: $errstr\n
      file: $errfile\n
      line: $errline\n
      backtrace:".$back_trace->toString();
    if(!$this->debug_mode)
      $this->logger->makeEndRecord($error_str);
  }
  
  function setDebugMode($mode)
  {
    $this->debug_mode = $mode;  	
  }  
  
  function fullLogMode($mode)
  {
    $this->full_log_enabled = $mode;	
  }
  
  function run()
  {
    try {    	   	          

	    if($this->full_log_enabled)
	      $this->logger->makeStartRecord();
	      	      
	    if(!$this->lockJob())
        return $this->logger->makeConflictRecord();
	
	    ob_start();
	    echo $this->job->getName() . ' started' . PHP_EOL;
	    $result = $this->job->run();
	    $output = ob_get_contents();
	    ob_end_clean();

	    $this->unlockJob();
	
	    if($this->full_log_enabled)
	      $this->logger->makeEndRecord($result, $output);
    }
    catch (lmbException $e)
    {
      $this->logger->makeExceptionRecord($e->getNiceTraceAsString());
      throw $e;
    }
    
    if($this->debug_mode)
    {    	
      echo $output;
      var_dump(lmbCollection :: toFlatArray($this->logger->getRecords(10)));
    }
  }
}