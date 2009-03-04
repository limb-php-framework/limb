<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/form/form.inc.php');

/**
 * class WactMultipleFormComponent.
 *
 * @package wact
 * @version $Id: WactMultipleFormComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
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

