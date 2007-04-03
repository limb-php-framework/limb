<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStopwatch.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require(dirname(__FILE__) . '/lmbTimePoint.class.php');

class lmbStopwatch
{
  static protected $instance;
  protected $time_points = array();

  static function instance()
  {
    if(!self :: $instance)
      self :: $instance = new lmbStopwatch();

    return self :: $instance;
  }

  static function getTimePoints()
  {
    return self :: instance()->time_points;
  }

  function markScriptStart()
  {
    self :: mark('Script start');
  }

  function mark($description = '')
  {
    self :: instance()->time_points[] = new lmbTimePoint($description);
  }
}

?>
