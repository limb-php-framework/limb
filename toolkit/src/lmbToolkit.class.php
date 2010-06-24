<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');
lmb_require('limb/toolkit/src/lmbRegistry.class.php');
lmb_require('limb/core/src/lmbObject.class.php');

/**
 * Toolkit is an implementation of Dinamic Service Locator pattern
 * The idea behind lmbToolkit class is simple:
 *  1) lmbToolkit is a Singleton
 *  2) lmbToolkit consists of so called tools. Tools is an object of any class that supports {@link lmbToolkitTools} interface
 *  3) lmbToolkit redirects all non existing methods via magic __call to tools if these methods were named in $tools :: getToolsSignatures() result.
 *  4) lmbToolkit also acts as a registry. You can put any data into toolkit and get them out at any place of your application
 * As a result we get an easily accessible object that we can extend with any methods we need.
 * We can also replace one tools with others thus we can return to client code completely different results from the same toolkit methods.
 * lmbToolkit also supports magic getters and setters. Say you have tools with getVar() method and you call $toolkit->get('var') then tools->getVar() will be actually called
 * Example of usage:
 * <code>
 * lmb_require('limb/net/src/lmbNetTools.class.php');
 * lmbToolkit :: merge(new lmbNetTools());
 * lmb_require('limb/net/src/toolkit/lmbDbTools.class.php');
 * lmbToolkit :: merge(new lmbDbTools());
 * // somethere in client code
 * $toolkit = lmbToolkit :: instance();
 * $toolkit->set('my_var', $value)'
 * $request = $toolkit->getRequest(); // supported by lmbNetTools
 * $same_request = $toolkit->get('requets'); // will delegate to getRequest()
 * $db_connection = $toolkit->getDefaultDbConnection(); // supported by lmbDbTools
 * $toolkit->get('my_var'); // returns $value value
 * </code>
 * @see lmbToolkitTools
 * @package toolkit
 * @version $Id: lmbToolkit.class.php 8177 2010-04-23 18:10:17Z conf $
 */
class lmbToolkit extends lmbObject
{
  /**
  * @var lmbToolkit Toolkit singleton instance
  */
  static protected $_instance = null;
  /**
  * @var array Current tools array
  */
  protected $_tools = array();
  /**
  * @var array Cached tools signatures that is methods supported by tools
  */
  protected $_tools_signatures = array();
  /**
  * @var boolean Flag if tools signatures were precached
  */
  protected $_signatures_loaded = false;
  /**
  * @var string Unique id of this toolkit
  */
  protected $_id;

  function __construct()
  {
    $this->_id = uniqid();
  }

  /**
  * Follows Singleton pattern interface
  * Returns toolkit instance. Takes instance from {@link lmbRegistry)
  * If instance is not initialized yet - creates one with empty tools
  * @see lmbRegistry
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function instance()
  {
    if(is_object(self :: $_instance))
      return self :: $_instance;

    self :: $_instance = new lmbToolkit();
    return self :: $_instance;
  }

  /**
  * Sets new tools object and clear signatures cache
  * @param lmbToolkitTools
  */
  protected function setTools($tools)
  {
    if(!is_array($tools))
      $this->_tools = array($tools);
    else
      $this->_tools = $tools;

    $this->_tools_signatures = array();
    $this->_signatures_loaded = false;
  }

  /**
  * Fills toolkit instance with suggested tools and registers this tools in {@ling lmbRegisty}
  * @see lmbRegistry
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function setup($tools)
  {
    $toolkit = lmbToolkit :: instance();
    $toolkit->setTools($tools);

    return $toolkit;
  }

  /**
  * Save current tools object in registry stack and creates a new one using currently saved empty copy of tools object
  * @see lmbRegistry :: save()
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function save()
  {
    $toolkit = lmbToolkit :: instance();

    $tools = $toolkit->_tools;
    $tools_copy = array();
    foreach($toolkit->_tools as $tool)
      $tools_copy[] = clone($tool);

    lmbRegistry :: set('__tools' . $toolkit->_id, $tools);
    lmbRegistry :: save('__tools' . $toolkit->_id);
    $toolkit->setTools($tools_copy);

    lmbRegistry :: set('__props' . $toolkit->_id, $toolkit->export());
    lmbRegistry :: save('__props' . $toolkit->_id);

    return $toolkit;
  }

  /**
  * Restores previously saved tools object instance from {@link lmbRegistry} stack and sets this tools into toolkit instance
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function restore()
  {
    $toolkit = lmbToolkit :: instance();

    lmbRegistry :: restore('__tools' . $toolkit->_id);
    $tools = lmbRegistry :: get('__tools' . $toolkit->_id);
    lmbRegistry :: restore('__props' . $toolkit->_id);
    $props = lmbRegistry :: get('__props' . $toolkit->_id);

    if($props !== null)
    {
      $toolkit->reset();
      $toolkit->import($props);
    }

    if($tools !== null)
      $toolkit->setTools($tools);

    return $toolkit;
  }

  /**
  * Extends current tools with new tool
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function merge($tool)
  {
    $toolkit = lmbToolkit :: instance();
    $toolkit->add($tool);
    return $toolkit;
  }

  /**
  * Extends current tools with new tool
  */
  function add($tool)
  {
    $tools = $this->_tools;
    array_unshift($tools, $tool);
    $this->setTools($tools);
  }

  /**
  * Sets variable into toolkit
  * Checks if appropriate setter method in tools exists to delegate to
  * @return void
  */
  function set($var, $value)
  {
    if($method = $this->_mapPropertyToSetMethod($var))
      return $this->$method($value);
    else
      return parent :: set($var, $value);
  }

  /**
  * Gets variable from toolkit
  * Checks if appropriate getter method in tools exists to delegate to
  * @return mixed
  */
  function get($var, $default = LIMB_UNDEFINED)
  {
    if($method = $this->_mapPropertyToGetMethod($var))
      return $this->$method();
    else
      return parent :: get($var, $default);
  }

  function has($var)
  {
    return $this->_hasGetMethodFor($var) || parent :: has($var);
  }

  /**
  * Sets variable into toolkit directly
  * @return void
  */
  function setRaw($var, $value)
  {
    return parent :: _setRaw($var, $value);
  }

  /**
  * Gets variable from toolkit directly
  * @return mixed
  */
  function getRaw($var)
  {
    return parent :: _getRaw($var);
  }

  /**
  * Magic caller. Delegates to {@link $tools} if $tools_signatures has required method
  * @param string Method name
  * @param array Method arguments
  * @return mixed
  */
  public function __call($method, $args = array())
  {
    $this->_ensureSignatures();

    if(isset($this->_tools_signatures[$method]))
      return call_user_func_array(array($this->_tools_signatures[$method], $method), $args);

    throw new lmbNoSuchMethodException("No such method '$method' exists in toolkit");
  }

  /**
  * Caches tools signatures. Fills {@link $tools_signatures}.
  * @see lmbToolkitTools :: getToolsSignatures()
  * @return void
  */
  protected function _ensureSignatures()
  {
    if($this->_signatures_loaded)
      return;

    $this->_tools_signatures = array();
    foreach($this->_tools as $tool)
    {
      $signatures = $tool->getToolsSignatures();
      foreach($signatures as $method => $obj)
      {
        if(!isset($this->_tools_signatures[$method]))
          $this->_tools_signatures[$method] = $obj;
      }
    }

    $this->_signatures_loaded = true;
  }

  protected function _hasGetMethodFor($property)
  {
    return (bool) $this->_mapPropertyToGetMethod($property);
  }

  protected function _mapPropertyToGetMethod($property)
  {
    $this->_ensureSignatures();

    $capsed = lmb_camel_case($property);
    $method = 'get' . $capsed;
    if(isset($this->_tools_signatures[$method]))
      return $method;
  }

  protected function _mapPropertyToSetMethod($property)
  {
    $this->_ensureSignatures();

    $method = 'set' . lmb_camel_case($property);
    if(isset($this->_tools_signatures[$method]))
      return $method;
  }
}

