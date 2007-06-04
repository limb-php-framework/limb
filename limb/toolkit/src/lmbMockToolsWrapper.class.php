<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMockToolsWrapper.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* Helps in introducing mock tools (mock objects generated for real tools) into lmbToolkit
* Created for testing purposes only.
* Example of usage:
* <code>
* Mock :: generate('MyTools', 'MockMyTools');
* $tools = new MockMyTools();
* $tools->expectOnce('getUser');
* $tools->setReturnValue('getUser', $user);
* $tools = new lmbMockToolsWrapper($tools, array('getUser'));
* lmbToolkit :: merge($tools);
* </code>
*/
class lmbMockToolsWrapper implements lmbToolkitTools
{
  /**
  * @var mixed Mock object generated for some real tools class
  */
  protected $mock;
  /**
  * @var array Array of methods that this tools wrapper allowed to support
  */
  protected $use_only_methods;

  /**
  * @param mixed Mock object generated for some real tools class
  * @param array Array of methods that this tools wrapper allowed to support
  */
  function __construct($mock, $use_only_methods = array())
  {
    $this->mock = $mock;
    $this->use_only_methods = $use_only_methods;
  }

  /**
  * @see lmbToolkitTools :: getToolsSignatures()
  */
  function getToolsSignatures()
  {
    $signatures = array();
    foreach(get_class_methods(get_class($this->mock)) as $method)
    {
      if($this->use_only_methods && !in_array($method, $this->use_only_methods))
        continue;

      $signatures[$method] = $this->mock;
    }
    return $signatures;
  }
}

?>
