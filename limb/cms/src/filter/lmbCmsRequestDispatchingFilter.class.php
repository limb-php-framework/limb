<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */
lmb_require('limb/web_app/src/request/lmbRoutesRequestDispatcher.class.php');
lmb_require('limb/cms/src/request/lmbCmsNodeBasedRequestDispatcher.class.php');
lmb_require('limb/web_app/src/filter/lmbRequestDispatchingFilter.class.php');
lmb_require('limb/web_app/src/request/lmbCompositeRequestDispatcher.class.php');

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

?>