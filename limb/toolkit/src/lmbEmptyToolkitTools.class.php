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
 * @version $Id: lmbEmptyToolkitTools.class.php 6238 2007-08-28 13:13:39Z pachanga $
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

