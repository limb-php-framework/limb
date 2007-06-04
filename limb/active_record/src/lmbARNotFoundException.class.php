<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARNotFoundException.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
class lmbARNotFoundException extends lmbARException
{
  protected $id;
  protected $class;

  function __construct($class, $id)
  {
    $this->id = $id;
    $this->class = $class;

    parent :: __construct("Can't load ActiveRecord '" . $class . "' with id '$id'");
  }

  function getId()
  {
    return $this->id;
  }

  function getClass()
  {
    return $this->class;
  }
}

?>