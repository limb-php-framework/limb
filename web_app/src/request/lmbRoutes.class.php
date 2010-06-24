<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbRoutes.
 *
 * @package web_app
 * @version $Id: lmbRoutes.class.php 8086 2010-01-22 01:32:51Z korchasa $
 */
class lmbRoutes
{
  protected $config = array();
  const NAMED_PARAM_REGEXP = '(?:\/([^\/]+))?';
  const EXTRA_PARAM_REGEXP = '(?:\/(.*))?';

  function __construct($config)
  {
    $this->config = $config;
  }

  function dispatch($url)
  {
    foreach($this->config as $route)
    {
      if(($result = $this->_getResultMatchedParams($route, $url)) === null)
        continue;

      if(!$this->_routeParamsMeetRequirements($route, $result))
        continue;

      return $this->_applyDispatchFilter($route, $result);
    }

    return array();
  }

  function toUrl($params, $route_name = '')
  {
    if($route_name && isset($this->config[$route_name]))
    {
      if($path = $this->_makeUrlByRoute($params, $this->config[$route_name]))
        return $path;
    }
    elseif(!$route_name)
    {
      foreach($this->config as $name => $route)
      {
        if($path = $this->_makeUrlByRoute($params, $route))
          return $path;
      }
    }
    throw new lmbException($message = "Route '$route_name' not found for params '" . lmb_var_dump($params) . "'");
  }

  protected function _applyDispatchFilter($route, $dispatched)
  {
    if(!isset($route['dispatch_filter']) && !isset($route['rewriter']))
      return $dispatched;

    //'rewriter' is going to be obsolete
    $filter = isset($route['dispatch_filter']) ? $route['dispatch_filter'] : $route['rewriter'];

    if(!is_callable($filter))
      throw new lmbException('Dispatch filter is not callable!', array('filter' => $filter));

    call_user_func_array($filter, array(&$dispatched, $route));
    return $dispatched;
  }

  protected function _applyUrlFilter($route, $path)
  {
    if(!isset($route['url_filter']))
      return $path;

    $filter = $route['url_filter'];

    if(!is_callable($filter))
      throw new lmbException('Url filter is not callable!', array('filter' => $filter));

    call_user_func_array($filter, array(&$path, $route));
    return $path;
  }

  protected function _getResultMatchedParams($route, $url)
  {
    if(($matched_params = $this->_getMatchedParams($route, $url)) === null)
      return null;

    if(isset($route['defaults']))
      return array_merge($route['defaults'], $matched_params);
    else
      return $matched_params;
  }

  function _getMatchedParams($route, $url)
  {
    $named_params = array();

    $regexp = $this->_getRouteRegexp($route['path'], $named_params);

    if(!preg_match($regexp, $url, $matched_params))
      return null;

    if (array_filter($matched_params)!=$matched_params)
      return null;

    array_shift($matched_params);

    $result = array();

    $index = 0;
    foreach($matched_params as $matched_item)
      if($param_name = $named_params[$index++])
        $result[$param_name] = urldecode($matched_item);

    return $result;
  }

  function _getRouteRegexp($route_path, &$named_params)
  {
    $elements = array();
    foreach (explode('/', $route_path) as $element)
      if (trim($element))
        $elements[] = $element;

    $final_regexp_parts = array();

    foreach ($elements as $element)
    {
      if($name = $this->_getNamedUrlParam($element))
      {
        $final_regexp_parts[] = '(?:\/'. preg_replace('/:'. $name .':?/', '([^\/]+)', $element). ')?';
        $named_params[] = $name;
      }
      elseif ($name = $this->_getExtraNamedParam($element))
      {
        $final_regexp_parts[] = self :: EXTRA_PARAM_REGEXP;
        $named_params[] = $name;
      }
      else
        $final_regexp_parts[] = '/' . $element;
    }

    return '#^' . implode('', $final_regexp_parts) . '[\/]*$#';
  }

  protected function _getNamedUrlParam($element)
  {
    if(preg_match('/^[^:]*:([^:]+):?.*$/', $element, $matches))
      return $matches[1];
    else
      return null;
  }

  protected function _getExtraNamedParam($element)
  {
    if(preg_match('/^\*(.+)?$/', $element, $matches))
    {
      if(isset($matches[1]))
        return $matches[1];
      else
        return 'extra';
    }
    else
      return null;
  }

  protected function _routeParamsMeetRequirements($route, $params)
  {
    foreach($params as $param_name => $param_value)
    {
      if(!$this->_singleParamMeetsRequirements($route, $param_name, $param_value))
        return false;
    }
    return true;
  }

  protected function _singleParamMeetsRequirements($route, $param_name, $param_value)
  {
     return (!isset($route['requirements'][$param_name]) ||
            preg_match($route['requirements'][$param_name], $param_value, $req_res));
  }

  function _makeUrlByRoute($params, $route)
  {
    $path = $route['path'];

    if(!$this->_routeParamsMeetRequirements($route, $params))
    {
      return "";
    }

    foreach($params as $param_name => $param_value)
    {
      if (isset($route['defaults'][$param_name]) && ($route['defaults'][$param_name] === $param_value)) {
        unset($params[$param_name]); // default params will be substituted lower
        continue;
      }

      if(strpos($path, ':'.$param_name) === false)
        continue;

      $path = preg_replace('/\:'. preg_quote($param_name) .'\:?/', $param_value, $path);
      unset($params[$param_name]);
    }

    if(count($params))
      return '';

    if(isset($route['defaults']))
    {
      // we define here required default params for building right url,
      // other params at the end of the path can be omitted.
      $required_params = array();
      if (preg_match_all('|(:\w+/?)+(?=/\w+)|', $path, $matched_params))
      {
        foreach($matched_params[0] as $param)
        {
          $required_params = array_merge(explode('/', $param), $required_params);
        }
      }

      foreach($route['defaults'] as $param_name => $param_value)
      {
        if(!in_array(':' . $param_name, $required_params))
          $param_value = '';

        $path = str_replace(':' . $param_name, $param_value, $path);
      }

      $path = preg_replace('~/+~', '/', $path);
    }

    if(strpos($path, "/:") !== false)
      return '';

    return $this->_applyUrlFilter($route, $path);
  }
}


