<?php
lmb_require('limb/log/lib/FirePHPCore/FirePHP.class.php');
lmb_require('limb/log/src/lmbLogWriter.interface.php');

class lmbLogFirePHPWriter extends FirePHP implements lmbLogWriter
{
	protected $check_client_extension;
	
	function __construct(lmbUri $dsn)
	{
		$this->check_client_extension = $dsn->getQueryItem('check_extension', 1);
	}
		
	function write(lmbLogEntry $entry)
	{
		return $this->fb($entry->asText());
	}
	
  protected function setHeader($name, $value)
  {
    lmbToolkit::instance()->getResponse()->addHeader($name.': '.$value);
  }
  
  function disableCheckClientExtension()
  {
    $this->check_client_extension = false;
  }
  
  function detectClientExtension()
  {
  	if($this->check_client_extension)
  	  return parent::detectClientExtension();
  	else
  	  return true;
  }
  
  function isClientExtensionCheckEnabled()
  {
  	return $this->check_client_extension;
  }
}