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
 * class SearchController.
 *
 * @package search
 * @version $Id: SearchController.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class SearchController extends lmbController
{
  function doDisplay()
  {
    $this->useForm('search_form');
  }
}


