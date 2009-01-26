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

class lmbSwishShellSearch
{
	protected $index;
  protected $swish = 'swish-e';
  protected $swish_proc;
  protected $swish_in;
  protected $swish_out;
  protected $headers = array('rank' => '<swishrank>',
                             'docpath' => '<swishdocpath>',
                             'title' => '<swishtitle>',
                             'time' => "<swishlastmodified fmt='%Y-%m-%d'>");

	function __construct($index)
	{
    $this->index = $index;
    if(!is_file($this->index))
      throw new Exception("Index file '{$this->index}' not found");
	}

  function debug($flag = true)
  {
    $this->debug = $flag;
  }

	function query($query)
	{
    $query = $this->_sanitize($query);
    $cmd = $this->_getCmd($query);
    $out = $this->_execCmd($cmd);

    if($this->debug)
      echo $out;

    return $this->_extractResults($out);
	}

  function _sanitize($query)
  {
		$query = escapeshellcmd($query);
		$query = stripslashes($query);
		$query = str_replace('"', '', $query);
    $query = str_replace("'", '', $query);
    return $query;
  }

  protected function _extractResults($out)
  {
    $total = 0;
    $res = array();
    $lines = explode("\n", $out);
    foreach($lines as $line)
    {
      if(preg_match('~^# Number of hits:\s*(\d+)~', $line, $m))
        $total = (int)$m[1];

      if(!$line || $line{0} == '#')
        continue;

      if(strpos($line, 'err:') === 0)  
        return array();
      
      if($line == '.')
        continue;

      $fields = explode('#', $line);

      $item = array();
      $c = 0;
      foreach(array_keys($this->headers) as $name)
      {
        $item[$name] = $fields[$c];
        $c++;
      }
      $res[] = $item;
    }
    return array($res, $total);
  }

	function _getCmd($query, $offset = null, $limit = null)
	{
    $headers = '';
    foreach($this->headers as $name => $header)
      $headers .= "$header#";
    $headers = rtrim($headers, '#');

    return $this->swish . ' -w ' . $query .
                          ' -f ' . $this->index .
                          ($limit ? ' -m ' . $limit : '') .
                          ($offset ? ' -b ' . $offset : '') .
                          " -x \"$headers\n\"";
	}

	function _execCmd($cmd)
	{
    if(!$proc = popen($cmd,"r"))
      throw new Exception("Proc open for command '$cmd' failed");

    $out = '';
		while($line = fgets($proc, 4096))
      $out .= $line;

    pclose($proc);
    return $out;
  }
}

