<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPHPView.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    view
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
?>
