<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Exception 'Image create is failed'
 * 
 * @package imagekit
 * @version $Id: lmbImageCreateFailedException.class.php 6553 2007-11-29 15:41:27Z cmz $
 */
class lmbImageLibraryNotInstalledException extends lmbException 
{

  function __construct($lib_name)
  {
  	parent::__construct('Library not installed', array('file' => $lib_name));
  }

}
