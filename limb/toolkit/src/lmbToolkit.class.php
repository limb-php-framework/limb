<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');
lmb_require('limb/toolkit/src/lmbRegistry.class.php');
lmb_require('limb/toolkit/src/lmbEmptyToolkitTools.class.php');
lmb_require('limb/toolkit/src/lmbCompositeToolkitTools.class.php');
lmb_require('limb/toolkit/src/lmbCompositeNonItersectingToolkitTools.class.php');
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');
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
 * @version $Id: lmbToolkit.class.php 6595 2007-12-06 20:10:05Z pachanga $
 */
class lmbToolkit extends lmbObject
{
  /**
  * @var lmbToolkitTools Current tools
  */
  protected $_tools;
  /**
  * @var array Cached tools signatures that is methods supported by tools
  */
  protected $_tools_signatures = array();
  /**
  * @var boolean Flag if tools signatures were precached
  */
  protected $_signatures_loaded = false;

  /**
  * Sets new tools object and clear signatures cache
  * @param lmbToolkitTools
  */
  protected function setTools($tools)
  {
    $this->_tools = $tools;
    $this->_tools_signatures = array();
    $this->_signatures_loaded = false;
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
    self :: _ensureInstance();
    return lmbRegistry :: get(__CLASS__);
  }

  /**
  * Ensures that instance of lmbToolkit class exists.
  * If instance is not initialized yet - creates one with empty tools
  * @see lmbRegistry
  * @return void
  */
  static protected function _ensureInstance()
  {
    $instance = lmbRegistry :: get(__CLASS__);

    if(is_object($instance))
      return;

    $instance = new lmbToolkit();
    lmbRegistry :: set(__CLASS__, $instance);

    $instance->setTools($tools = new lmbEmptyToolkitTools());

    lmbRegistry :: set('lmbToolkitTools', $tools);
    lmbRegistry :: set('lmbToolkitToolsCopy', clone($tools));
    lmbRegistry :: set('lmbToolkitProperties', array());
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

    lmbRegistry :: set('lmbToolkitTools', $tools);
    lmbRegistry :: set('lmbToolkitToolsCopy', clone($tools));

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

    $tools = lmbRegistry :: get('lmbToolkitToolsCopy');
    $tools_copy = clone($tools);

    $toolkit->setTools($tools_copy);

    lmbRegistry :: save('lmbToolkitTools');
    lmbRegistry :: save('lmbToolkitToolsCopy');

    lmbRegistry :: set('lmbToolkitTools', $tools);
    lmbRegistry :: set('lmbToolkitToolsCopy', $tools_copy);

    lmbRegistry :: set('lmbToolkitProperties', $toolkit->export());
    lmbRegistry :: save('lmbToolkitProperties');
    $toolkit->reset(array());

    return $toolkit;
  }

  /**
  * Restores previously saved tools object instance from {@link lmbRegistry} stack and sets this tools into toolkit instance
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function restore()
  {
    $toolkit = lmbToolkit :: instance();

    lmbRegistry :: restore('lmbToolkitTools');
    lmbRegistry :: restore('lmbToolkitToolsCopy');
    lmbRegistry :: restore('lmbToolkitProperties');

    $tools = lmbRegistry :: get('lmbToolkitTools');
    $toolkit->setTools($tools);

    $toolkit->import(lmbRegistry :: get('lmbToolkitProperties'));

    return $toolkit;
  }

  /**
  * Extends current tools with new tools
  * Merges tools together using {@link lmbCompositeNonItersectingToolkitTools} that doesn't allow tools methods intersection
  * @see lmbCompositeNonItersectingToolkitTools
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function extend($tools)
  {
    self :: _ensureInstance();

    $tools_copy = lmbRegistry :: get('lmbToolkitToolsCopy');
    return self :: setup(new lmbCompositeNonItersectingToolkitTools($tools_copy, $tools));
  }

  /**
  * Extends current tools with new tools
  * Merges tools together using {@link lmbCompositeToolkitTools}
  * @see lmbCompositeToolkitTools
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function merge($tools)
  {
    self :: _ensureInstance();

    $tools_copy = lmbRegistry :: get('lmbToolkitToolsCopy');
    return self :: setup(new lmbCompositeToolkitTools($tools_copy, $tools));
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
  * @return void
  */
  function get($var, $default = LIMB_UNDEFINED)
  {
    if($method = $this->_mapPropertyToGetMethod($var))
      return $this->$method();
    else
      return parent :: get($var, $default);
  }

  function setRaw($var, $value)
  {
    return parent :: _setRaw($var, $value);
  }

  function getRaw($var)
  {
    return parent :: _getRaw($var);
  }


  function has($var)
  {
    return $this->_hasGetMethodFor($var) || parent :: has($var);
  }

  /**
  * Magic caller. Delegates to {@link $tools} if $tools_signatures has required method
  * @param string Method name
  * @param array Method arguments
  * @return mixed
  */
  function __call($method, $args)
  {
    $this->_ensureSignatures();

    if(isset($this->_tools_signatures[$method]))
      return call_user_func_array(array($this->_tools_signatures[$method], $method), $args);

    return parent :: __call($method, $args);
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

    $this->_tools_signatures = $this->_tools->getToolsSignatures();
    $this->_signatures_loaded = true;
  }

  protected function _hasGetMethodFor($property)
  {
    $this->_ensureSignatures();

    $capsed = lmb_camel_case($property);
    $method = 'get' . $capsed;
    return isset($this->_tools_signatures[$method]);
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

