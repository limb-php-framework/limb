<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package swishe
 * @version $Id$
 */
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbSwishIndexer
{
  protected static $swish = 'swish-e';
  protected static $stamp = '.stamp';
  protected static $verbose = false;
  protected $index;
  protected $swish_config;
  protected $swish_proc;
  protected $swish_in;
  protected $swish_out;
  protected $error_log;

  function __construct($index)
  {
    $this->index = $index;
  }

  static function setSwish($swish)
  {
    self :: $swish = $swish;
  }

  static function ensureSwish()
  {
    if(!file_exists(self :: $swish))
      throw new Exception("Swish binary not found at '" . self :: $swish . "'");
  }

  static function verbose($flag)
  {
    self :: $verbose = $flag;
  }

  function useErrorLog($error_log)
  {
    $this->error_log = $error_log;
  }

  function useConfig($config)
  {
    $this->swish_config = $config;
  }

  static function getIndexTime($index)
  {
    $dir = dirname($index);
    if(!is_dir($dir) || !is_file($dir . '/' . self :: $stamp))
      return -1;
    return file_get_contents($dir . '/' . self :: $stamp);
  }

  static function merge($index1, $index2, $result)
  {
    self :: ensureSwish();

    if(!is_file($index1))
      throw new Exception("Index file '$index1' doesn't exist");
    if(!is_file($index2))
      throw new Exception("Index file '$index2' doesn't exist");

    lmbFs :: mkdir(dirname($result));
    $cmd = self :: $swish . " -M $index1 $index2 $result";

    if(self :: $verbose)
      system($cmd, $ret);
    else
      exec($cmd, $out, $ret);

    if($ret != 0)
      throw new Exception("Merging of files '$index1' and '$index2' into '$result' failed");
  }

  static function mergeInto($index1, $index2)
  {
    $index_name = basename($index2);
    $index_dir = dirname($index2);
    $old_dir = $index_dir . '-old-' . mt_rand();
    $merged_dir = lmbFs :: getTmpDir() . '/merged-' . mt_rand();
    $merged = $merged_dir . '/' . $index_name;

    self :: merge($index1, $index2, $merged);

    lmbFs :: mv($index_dir, $old_dir);
    lmbFs :: mv($merged_dir, $index_dir);
    lmbFs :: rm($old_dir);
    lmbFs :: cp(dirname($index1) . '/' . self :: $stamp, $index_dir);
    lmbFs :: rm(dirname($index1));
  }

  function open()
  {
    self :: ensureSwish();

    lmbFs :: mkdir(dirname($this->index));
    file_put_contents(dirname($this->index) . '/' . self :: $stamp, time());
    $this->_openSwishProc();
  }

  function close()
  {
    $this->_closeSwishProc();
  }

  function add($uri, $time, $content, $type='HTML2')
  {
    $size = strlen($content);
    $header = <<<EOD
Path-Name: $uri
Content-Length: $size
Last-Mtime: $time
Document-Type: $type


EOD;
    $this->_writeToSwish($header . $content);
  }

  protected function _writeToSwish($content)
  {
    if(!is_resource($this->swish_proc))
      throw new Exception('Indexing session was not opened!');

    fwrite($this->swish_in, $content);
  }

  protected function _openSwishProc()
  {
    $descrs = array(
      0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
      1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
    );

    if($this->error_log)
      $descrs[2] = array("file", $this->error_log, "a");

    $this->swish_proc = proc_open(self :: $swish . " -f $this->index -S prog -i stdin" .
                                  ($this->swish_config ? " -c $this->swish_config" : ''),
                                  $descrs, $pipes, $cwd = '.');

    if(!is_resource($this->swish_proc))
      throw new Exception('Could not open swish-e proc');

    $this->swish_in = $pipes[0];
    $this->swish_out = $pipes[1];
  }

  protected function _closeSwishProc()
  {
    if(!is_resource($this->swish_proc))
      throw new Exception('swish-e indesing session was not opened');

    fclose($this->swish_in);

    if(self :: $verbose)
      echo stream_get_contents($this->swish_out);

    fclose($this->swish_out);

    $return_value = proc_close($this->swish_proc);
    return $return_value;
  }
}

