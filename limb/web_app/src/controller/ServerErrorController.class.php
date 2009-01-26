<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');

/**
 * class ServerErrorController.
 *
 * @package web_app
 * @version $Id$
 */
class ServerErrorController extends lmbController
{
  function doDisplay()
  {
    $this->response->addHeader('HTTP/1.x 500 Server Error');
    $this->resetView();
    $this->setTemplate('server_error.html');
  }
}


