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
 * @version $Id: NotFoundController.class.php 7111 2008-07-10 09:34:17Z korchasa $
 */
class NotFoundController extends lmbController
{
  function doDisplay()
  {
    $this->response->addHeader('HTTP/1.x 404 Not Found');
    $this->setTemplate($this->findTemplateByAlias('not_found'));
  }
}


