<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmptyToolkitTools.class.php 5141 2007-02-19 22:13:31Z serega $
 * @package    toolkit
 */

/**
* Empty tools. Supports no methods.
*/
class lmbEmptyToolkitTools implements lmbToolkitTools
{
  /**
  * @see lmbToolkitTools :: getToolsSignatures()
  */
  function getToolsSignatures()
  {
    return array();
  }
}
?>
