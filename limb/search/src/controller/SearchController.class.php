<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: SearchController.class.php 5003 2007-02-08 15:36:51Z pachanga $
 * @package    search
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class SearchController extends lmbController
{
  function doDisplay()
  {
    $this->useForm('search_form');
  }
}

?>