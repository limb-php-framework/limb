<?php
lmb_require('limb/log/src/lmbFirePHP.class.php');
lmb_require('limb/log/src/lmbLogWriter.interface.php');
lmb_require('limb/net/src/lmbHttpResponse.class.php');

class lmbLogFirePHPWriter extends lmbFirePHP implements lmbLogWriter
{
  protected $check_client_extension;
  protected $_log_level;
  protected $_dsn;

  /**
   * @param lmbUri $dsn
   */
  function __construct(lmbUri $dsn)
  {
    $this->_dsn = $dsn;
    $this->_log_level = ($level = $this->_dsn->getQueryItem('level')) !== false ? $level : LOG_INFO;
    $this->check_client_extension = $dsn->getQueryItem('check_extension', 1);
  }

  /**
   * @param int $level
   */
  function setErrorLevel($level)
  {
    $this->_log_level = $level;
  }

  /**
   * @param lmbLogEntry $entry
   * @return boolean
   */
  function isAllowedLevel(lmbLogEntry $entry)
  {
    return $entry->getLevel() <= $this->_log_level;
  }

  /**
   * @param lmbLogEntry $entry
   * @return boolean
   */
  function write(lmbLogEntry $entry)
  {
    if($this->isAllowedLevel($entry))
      return $this->_write($entry);
  }

  /**
   * @param lmbLogEntry $entry
   * @return boolean
   */
  protected function _write(lmbLogEntry $entry)
  {
    return $this->fb(
       array(
         'Log level' => $entry->getLevelForHuman(),
         'Message' => $entry->getMessage(),
         'Additional attributes' => $entry->getParams(),
         'Back trace' => $entry->getBacktrace()->toString(),
         )
       );
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
