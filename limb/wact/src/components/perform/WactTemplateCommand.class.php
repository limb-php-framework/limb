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

class WactTemplateCommand
{
  protected $template;
  protected $context_component;

  function __construct($template, $context_component)
  {
    $this->template = $template;
    $this->context_component = $context_component;
  }

  function doPerform(){}
}
?>