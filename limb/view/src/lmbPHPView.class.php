<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbPHPView.
 *
 * @package view
 * @version $Id$
 */
class lmbPHPView
{
  protected $file;
  protected $vars = array();

  function __construct($file)
  {
    $this->file = $file;
  }

  function set($name, $value)
  {
    $this->vars[$name] = $value;
  }

  function out()
  {
    extract($this->vars);
    ob_start();
    include($this->file);
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }
}

