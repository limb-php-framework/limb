<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLog.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */
define('LIMB_MAX_LOGROTATE_FILES', 5);
define('LIMB_MAX_LOGFILE_SIZE', 500*1024);

lmb_require('limb/util/src/system/lmbFs.class.php');

class lmbLog
{
  function write($file_name, $message)
  {
    lmbFs :: mkdir(dirname($file_name), 0775);

    $oldumask = umask(0);
    $file_existed = file_exists($file_name);
    $log_file = fopen($file_name, 'a');

    if($log_file)
    {
      $time = strftime("%b %d %Y %H:%M:%S", strtotime('now'));

      $log_message = "=========================[{$time}]";

      if(isset($_SERVER['REMOTE_ADDR']))
        $log_message .= '[' . $_SERVER['REMOTE_ADDR'] . ']';

      if(isset($_SERVER['REQUEST_URI']))
        $log_message .= '[' . $_SERVER['REQUEST_URI'] . ']';

      $log_message .= "=========================\n" . $message;

      fwrite($log_file, $log_message);
      fclose($log_file);
      if(!$file_existed)
        chmod($file_name, 0664);

      umask($oldumask);
    }
    else
    {
      umask($oldumask);
      throw new lmbIOException("Cannot open log file '$file_name' for writing\n" .
                         "The web server must be allowed to modify the file.\n" .
                         "File logging for '$file_name' is disabled.");

    }
  }

  function rotate($file_name, $max_logrotate_files = LIMB_MAX_LOGROTATE_FILES)
  {
    for($i = $max_logrotate_files; $i > 0; --$i)
    {
      $log_rotate_name = $file_name . '.' . $i;
      if(@file_exists($log_rotate_name))
      {
        if($i == $max_logrotate_files)
        {
          @unlink($log_rotate_name);
        }
        else
        {
          $new_log_rotate_name = $file_name . '.' . ($i + 1);
          @rename($log_rotate_name, $new_log_rotate_name);
        }
      }
    }
    if(@file_exists($file_name))
    {
      $new_log_rotate_name = $file_name . '.' . 1;
      @rename( $file_name, $new_log_rotate_name );
      return true;
    }
    return false;
  }
}
?>