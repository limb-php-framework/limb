<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');

/**
 * class NotFoundController.
 *
 * @package web_app
 * @version $Id: NotFoundController.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class NotFoundController extends lmbController
{
  function doDisplay()
  {
    $this->response->header('HTTP/1.x 404 Not Found');
    $this->resetView();
    $this->setTemplate('not_found.html');
  }
}

?>
