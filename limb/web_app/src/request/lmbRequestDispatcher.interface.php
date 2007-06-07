<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbRequestDispatcher.
 *
 * @package web_app
 * @version $Id: lmbRequestDispatcher.interface.php 5959 2007-06-07 13:47:57Z pachanga $
 */
interface lmbRequestDispatcher
{
  function dispatch($request);
}

?>