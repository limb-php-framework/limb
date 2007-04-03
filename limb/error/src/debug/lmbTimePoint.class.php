<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTimePoint.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */

class lmbTimePoint
{
  protected $time;
  protected $memory_usage;
  protected $description;

  function __construct($description, $time = null, $memory = null)
  {
    $this->time = is_null($time) ? microtime() : $time;

    if(is_null($memory) && function_exists('memory_get_usage'))
      $this->memory_usage = memory_get_usage();
    else
      $this->memory_usage = $memory;

    $this->description = $description;
  }

  function timeToFloat()
  {
    $t_time = explode(' ', $this->time);
    preg_match("~0\.([0-9]+)~", '' . $t_time[0], $t1);
    $time = $t_time[1] . '.' . $t1[1];
    return $time;
  }
}

?>
