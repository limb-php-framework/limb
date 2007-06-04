<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestFileFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
