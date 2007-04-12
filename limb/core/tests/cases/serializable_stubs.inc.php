<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: serializable_stubs.inc.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */

class SerializableTestChildStub
{
  function identify()
  {
    return 'child';
  }
}

class SerializableTestStub
{
  protected $child;

  function __construct()
  {
    $this->child = new SerializableTestChildStub();
  }

  function identify()
  {
    return 'parent';
  }

  function getChild()
  {
    return $this->child;
  }
}

?>
