<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Exception 'Image save is failed'
 * 
 * @package imagekit
 * @version $Id: lmbImageSaveFailedException.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbImageSaveFailedException extends lmbException 
{

  function __construct($file_name)
  {
  	parent::__construct('Image save is failed', array('file' => $file_name));
  }

}
