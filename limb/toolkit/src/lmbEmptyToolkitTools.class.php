<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmptyToolkitTools.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
