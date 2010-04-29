<?php

lmb_require('limb/cron/src/lmbCronJobLogger.class.php');

abstract class lmbCronBaseJob
{
  protected $locks_dir;
  protected $lock_file_pointer;

  abstract function run();
  
  function __construct()
  {
  	$this->locks_dir = lmb_var_dir().'/cron_job_locks';	
  }	
  
  function getLocksDir()
  {
    return $this->locks_dir;
  }
  
  function setLocksDir($locks_dir)
  {
  	$this->locks_dir = $locks_dir;
  }
  
  function getName()
  {
  	return get_class($this);
  }
  
  function getLockFile()
  {
  	return $this->getLocksDir().$this->getName().'.pid';
  }
  
  function lock()
  {
    if(!file_exists($this->getLocksDir()))
      lmbFs::mkdir($this->getLocksDir());
  	  	
    $this->lock_file_pointer = fopen($this->getLockFile(), 'w');

    if(!flock($this->lock_file_pointer, LOCK_EX + LOCK_NB))
      return false;
    
    fwrite($this->lock_file_pointer, posix_getpid());
        
    return true;
  }
  
  function unlock()
  {
  	unlink($this->getLockFile());  	
  }
}