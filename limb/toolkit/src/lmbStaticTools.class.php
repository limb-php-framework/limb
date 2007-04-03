<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticTools.class.php 5141 2007-02-19 22:13:31Z serega $
 * @package    toolkit
 */

/**
* A special kind of tools that always returns some predefined result from each method
* Created for testing purposes mostly
* Example of usage:
* <code>
* $tools = new lmbStaticTools(array('getUser' => $mock_user, 'getDbConnection' => $mock_db_connection));
* lmbToolkit :: merge($tools);
* </code>
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

?>
