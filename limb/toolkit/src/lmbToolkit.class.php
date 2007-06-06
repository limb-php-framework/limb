<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require(dirname(__FILE__) . '/lmbToolkitTools.interface.php');
lmb_require(dirname(__FILE__) . '/lmbRegistry.class.php');
lmb_require(dirname(__FILE__) . '/lmbEmptyToolkitTools.class.php');
lmb_require(dirname(__FILE__) . '/lmbCompositeToolkitTools.class.php');
lmb_require(dirname(__FILE__) . '/lmbCompositeNonItersectingToolkitTools.class.php');

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
 * @version $Id: lmbToolkit.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbToolkit
{
  /**
  * @var lmbToolkitTools Current tools
  */
  protected $tools;
  /**
  * @var array Current set of toolkit data
  */
  protected $properties = array();
  /**
  * @var array Cached tools signatures that is methods supported by tools
  */
  protected $tools_signatures = array();
  /**
  * @var boolean Flag if tools signatures were precached
  */
  protected $signatures_loaded = false;

  /**
  * Sets new tools object and clear signatures cache
  * @param lmbToolkitTools
  */
  protected function setTools($tools)
  {
    $this->tools = $tools;
    $this->tools_signatures = array();
    $this->signatures_loaded = false;
  }

  /**
  * Sets new set of toolkit data
  * @param array
  */
  protected function setProperties($properties)
  {
    $this->properties = $properties;
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
  * Ensures that instance of lmbToolkit class is exists.
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

    lmbRegistry :: set('lmbToolkitProperties', $toolkit->properties);
    lmbRegistry :: save('lmbToolkitProperties');
    $toolkit->setProperties(array());

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

    $properties = lmbRegistry :: get('lmbToolkitProperties');
    $toolkit->setProperties($properties);

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
      $this->setRaw($var, $value);
  }

  /**
  * Gets variable from toolkit
  * Checks if appropriate getter method in tools exists to delegate to
  * @return void
  */
  function get($var)
  {
    if($method = $this->_mapPropertyToGetMethod($var))
      return $this->$method();
    else
      return $this->getRaw($var);
  }

  /**
  * Sets variable from toolkit
  * Doesn't check if appropriate setter method in tools exists to delegate to
  * @return void
  */
  function setRaw($var, $value)
  {
    $this->properties[$var] = $value;
  }

  /**
  * Gets variable from toolkit
  * Doesn't check if appropriate getter method in tools exists to delegate to
  * @return void
  */
  function getRaw($var)
  {
    if(isset($this->properties[$var]))
      return $this->properties[$var];
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

    if(!isset($this->tools_signatures[$method]))
      throw new lmbException('toolkit does not support method "' . $method . '" (no such signature)',
                              array('method' => $method));


    return call_user_func_array(array($this->tools_signatures[$method], $method), $args);
  }

  /**
  * Caches tools signatures. Fills {@link $tools_signatures}.
  * @see lmbToolkitTools :: getToolsSignatures()
  * @return void
  */
  protected function _ensureSignatures()
  {
    if($this->signatures_loaded)
      return;

    $this->tools_signatures = $this->tools->getToolsSignatures();
    $this->signatures_loaded = true;
  }

  protected function _mapPropertyToGetMethod($property)
  {
    $this->_ensureSignatures();

    $capsed = lmb_camel_case($property);
    $method = 'get' . $capsed;
    if(isset($this->tools_signatures[$method]))
      return $method;
  }

  protected function _mapPropertyToSetMethod($property)
  {
    $this->_ensureSignatures();

    $method = 'set' . lmb_camel_case($property);
    if(isset($this->tools_signatures[$method]))
      return $method;
  }

}
?>
