<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Exception 'Image save is failed'
 * 
 * @package imagekit
 * @version $Id: lmbImageSaveFailedException.class.php 6553 2007-11-29 15:41:27Z cmz $
 */
class lmbImageSaveFailedException extends lmbException 
{

  function __construct($file_name)
  {
  	parent::__construct('Image save is failed', array('file' => $file_name));
  }

}
?>