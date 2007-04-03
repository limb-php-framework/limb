<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: NotFoundController.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');

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
