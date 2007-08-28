<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * A special kind of tools that always returns some predefined result from each method
 * Created for testing purposes mostly
 * Example of usage:
 * <code>
 * $tools = new lmbStaticTools(array('getUser' => $mock_user, 'getDbConnection' => $mock_db_connection));
 * lmbToolkit :: merge($tools);
 * </code>
 * @package toolkit
 * @version $Id: lmbStaticTools.class.php 6238 2007-08-28 13:13:39Z pachanga $
 */
class lmbStaticTools implements lmbToolkitTools
{
  /**
  * @var array Array of method results
  */
  protected $call_results;

  /**
  * Constructor
  * @param array Array of method results that should be returned in response to these methods calls
  */
  function __construct($call_results)
  {
    $this->call_results = $call_results;
  }

  /**
  * @see lmbToolkitTools :: getToolsSignatures()
  */
  function getToolsSignatures()
  {
    $signatures = array();
    foreach(array_keys($this->call_results) as $method)
    {
      $signatures[$method] = $this;
    }
    return $signatures;
  }

  /**
  * Magic caller. Simply returns result from {@link $call_results} attribute
  * @param string Method name
  * @param array Method arguments
  * @return mixed
  */
  function __call($method, $args)
  {
    return $this->call_results[$method];
  }
}


