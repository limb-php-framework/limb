<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbCmsBreadcrumbsFetcher extends lmbFetcher
{
  static protected $breadcrumbs;
  protected $handlers = array();
  protected $toolkit;

  function __construct()
  {
    $this->toolkit = lmbToolkit :: instance();
    parent :: __construct();
  }

  function addRoute($title, $params = array(), $route = '')
  {
    $this->addUrl($title, $this->_getRoutePath($params, $route));
  }

  function addUrl($title, $path)
  {
    self :: $breadcrumbs[] = array('title' => $title, 'path' => $path);
  }

  function need($controller, $action, $params = array())
  {
    $handle = 'crumb_for_' . $controller . '_' . $action;
    array_push($this->handlers, array($controller, $action));
    if(is_callable($handle))
      $handle($this, $params);
    array_pop($this->handlers);
  }

  function _createDataSet()
  {
    $this->_makeCrumbs();
    return new lmbCollection(self :: $breadcrumbs);
  }

  function _getRoutePath($params = array(), $route = '')
  {
    $item = end($this->handlers);
    $params['controller'] = $item[0];
    $params['action'] = $item[1];

    return $this->toolkit->getRoutesUrl($params, $route);
  }

  function _makeCrumbs()
  {
    if(!is_null(self :: $breadcrumbs))
      return;

    self :: $breadcrumbs = array();

    $this->toolkit->getConf('crumbs');

    $request = $this->toolkit->getRequest();
    $controller = $this->toolkit->getDispatchedController();

    $this->need(lmb_under_scores($controller->getName()),
                lmb_under_scores($controller->getCurrentAction()),
                $request);
    self :: $breadcrumbs[count(self :: $breadcrumbs) - 1]['is_last'] = true;
  }
}

?>