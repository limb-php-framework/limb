<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARNotFoundException.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
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