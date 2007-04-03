<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestFileFilter.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */

class lmbTestFileFilter
{
  protected $regex;

  function __construct($filters)
  {
    $this->regex = $this->_createRegex($filters);
  }

  function match($item)
  {
    return preg_match($this->regex, basename($item));
  }

  protected function _createRegex($filters)
  {
    $regex = implode('|', $filters);
    $regex = preg_quote($regex);
    $regex = str_replace(array('\*', '\|'), array('.*', '|'), $regex);
    return '~^(?:' . $regex. ')$~';
  }
}

?>
