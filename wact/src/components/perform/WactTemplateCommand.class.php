<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactTemplateCommand.
 *
 * @package wact
 * @version $Id$
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

