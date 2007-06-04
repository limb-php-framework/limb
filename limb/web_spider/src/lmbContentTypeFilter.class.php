<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbContentTypeFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class lmbContentTypeFilter
{
  protected $allowed_types;

  function lmbContentTypeFilter()
  {
    $this->reset();
  }

  function reset()
  {
    $this->allowed_types = array();
  }

  function allowContentType($type)
  {
    $this->allowed_types[] = strtolower($type);
  }

  function canPass($type)
  {
    if(!in_array($type, $this->allowed_types))
      return false;

    return true;
  }
}

?>
