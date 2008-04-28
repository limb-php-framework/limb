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
 * @version $Id: lmbImageTypeNotSupportedException.class.php 6963 2008-04-28 04:04:31Z svk $
 */
class lmbImageTypeNotSupportedException extends lmbException 
{

  function __construct($type = '')
  {
  	parent::__construct('Image type is not supported', $type ? array('type' => $type) : array());
  }

}
