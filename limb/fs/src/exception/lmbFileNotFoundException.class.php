<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileNotFoundException.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

class lmbFileNotFoundException extends lmbException
{
  function __construct($file_path, $message = 'file not found', $params = array())
  {
    $this->_file_path = $file_path;

    $params['file_path'] = $file_path;

    parent :: __construct($file_path . ': ' . $message, $params);
  }

  function getFilePath()
  {
    return $this->_file_path;
  }
}

?>