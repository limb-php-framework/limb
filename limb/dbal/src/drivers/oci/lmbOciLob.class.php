<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciLob.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

abstract class lmbOciLob
{
  protected $value;

  function __construct($value)
  {
    $this->value = $value;
  }

  abstract function getNativeType();
  abstract function getEmptyExpression();
  abstract function getDescriptorType();

  function read()
  {
    return $this->value;
  }
}

?>
