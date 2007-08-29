<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class lmbLogFileWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogFileWriter
{
  protected $log_files;

  function __construct($log_dir)
  {
    $this->log_files = array(
      lmbLog :: NOTICE => $log_dir . '/notice.log',
      lmbLog :: WARNING => $log_dir . '/warning.log',
      lmbLog :: ERROR => $log_dir . '/error.log',
      lmbLog :: INFO => $log_dir . '/debug.log'
    );
  }

  function write($entry)
  {
    $this->_appendToFile($this->getLogFile($entry->getLevel()),
                         $entry->asText(),
                         $entry->getTime());
  }

  protected function _appendToFile($file_name, $message, $stamp)
  {
    lmbFs :: mkdir(dirname($file_name), 0775);
    $file_existed = file_exists($file_name);

    if($fh = fopen($file_name, 'a'))
    {
      @flock($fh, LOCK_EX);
      $time = strftime("%b %d %Y %H:%M:%S", $stamp);

      $log_message = "=========================[{$time}]";

      if(isset($_SERVER['REMOTE_ADDR']))
        $log_message .= '[' . $_SERVER['REMOTE_ADDR'] . ']';

      if(isset($_SERVER['REQUEST_URI']))
        $log_message .= '[' . $_SERVER['REQUEST_URI'] . ']';

      $log_message .= "=========================\n" . $message;

      fwrite($fh, $log_message);
      @flock($fp, LOCK_UN);
      fclose($fh);
      if(!$file_existed)
        chmod($file_name, 0664);
    }
    else
    {
      throw new lmbFsException("Cannot open log file '$file_name' for writing\n" .
                               "The web server must be allowed to modify the file.\n" .
                               "File logging for '$file_name' is disabled.");
    }
  }

  function getLogFile($level)
  {
    if(isset($this->log_files[$level]))
      return $this->log_files[$level];
  }
}


