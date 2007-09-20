<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Abstract image container
 *
 * @package imagekit
 * @version $Id$
 */
abstract class lmbAbstractImageContainer 
{

  function __construct($file_name, $type = '')
  {
    $this->load($file_name, $type);
  }

  abstract function load($file_name, $type = '');

  abstract function save($file_name = null, $type = '');

}
?>