<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbRoutesRequestDispatcher.class.php');
lmb_require('limb/cms/src/request/lmbCmsNodeBasedRequestDispatcher.class.php');
lmb_require('limb/web_app/src/filter/lmbRequestDispatchingFilter.class.php');
lmb_require('limb/web_app/src/request/lmbCompositeRequestDispatcher.class.php');

/**
 * class lmbCmsRequestDispatchingFilter.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsRequestDispatchingFilter extends lmbRequestDispatchingFilter
{
  function __construct()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $dispatcher->addDispatcher(new lmbCmsNodeBasedRequestDispatcher());
    $dispatcher->addDispatcher(new lmbRoutesRequestDispatcher());
    parent :: __construct($dispatcher);
  }
}


