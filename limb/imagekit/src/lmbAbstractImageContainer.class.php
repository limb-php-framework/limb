<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Abstract image container
 *
 * @package imagekit
 * @version $Id: lmbAbstractImageContainer.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class lmbAbstractImageContainer
{

  protected $output_type = '';

  function setOutputType($type)
  {
    $this->output_type = $type;
  }

  function getOutputType()
  {
    return $this->output_type;
  }

  abstract function load($file_name, $type = '');

  abstract function save($file_name = null);

}
