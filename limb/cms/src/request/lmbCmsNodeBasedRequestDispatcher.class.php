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

/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

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

?>