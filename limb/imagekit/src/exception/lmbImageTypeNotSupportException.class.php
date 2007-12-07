<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Exception 'Image type is not supported'
 * 
 * @package imagekit
 * @version $Id: lmbImageTypeNotSupportException.class.php 6598 2007-12-07 08:01:45Z pachanga $
 */
class lmbImageTypeNotSupportException extends lmbException 
{

  function __construct($type = '')
  {
  	parent::__construct('Image type is not supported', $type ? array('type' => $type) : array());
  }

}
?>
