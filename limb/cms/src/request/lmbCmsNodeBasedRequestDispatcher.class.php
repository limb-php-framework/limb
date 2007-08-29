<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

/**
 * class lmbCmsNodeBasedRequestDispatcher.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsNodeBasedRequestDispatcher implements lmbRequestDispatcher
{
  function dispatch($request)
  {
    $result = array();

    if(!$node = lmbCmsNode :: findByPath($request->getUriPath()))
       return $result;

    $result['controller'] = $node->getControllerName();

    if($action = $request->get('action'))
      $result['action'] = $action;

    return $result;
  }
}


