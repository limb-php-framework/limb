<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbToolkitTools.interface.php 5141 2007-02-19 22:13:31Z serega $
 * @package    toolkit
 */

/**
* Interface for defining toolkit tools that extends lmbToolkit
* @see lmbToolkit
*/
interface lmbToolkitTools
{
  /**
  * @return array Array of method names with reference to itself, something like array('getUser' => $this, 'getTree' => $this)
  */
  function getToolsSignatures();
}
?>
