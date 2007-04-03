<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactMultipleFormComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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