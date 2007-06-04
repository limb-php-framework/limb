<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactMultipleFormComponent.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/src/components/form/form.inc.php');

class WactMultipleFormComponent extends WactFormComponent
{
  function getValue($name)
  {
    return parent :: getValue($this->getNonWrappedName($name));
  }

  function setValue($name, $value)
  {
    parent :: setValue($this->getNonWrappedName($name), $value);
  }

  function getNonWrappedName($name)
  {
    $name = str_replace(array("[", "]"), array(".", ""), $name);
    return end(explode(".", $name));
  }
}
?>