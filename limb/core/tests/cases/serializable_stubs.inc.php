<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
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
