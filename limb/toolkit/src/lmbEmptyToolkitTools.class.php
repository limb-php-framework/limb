<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Empty tools. Supports no methods.
 * @package toolkit
 * @version $Id: lmbEmptyToolkitTools.class.php 5945 2007-06-06 08:31:43Z pachanga $
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
