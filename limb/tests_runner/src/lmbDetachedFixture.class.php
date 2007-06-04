<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDetachedFixture.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class lmbDetachedFixture
{
  protected $_setup;
  protected $_teardown;
  protected $_container = array();

  function __construct($setup, $teardown)
  {
    $this->_setup = $setup;
    $this->_teardown = $teardown;
  }

  function setUp()
  {
    if(file_exists($this->_setup))
      include($this->_setup);
  }

  function tearDown()
  {
    if(file_exists($this->_teardown))
      include($this->_teardown);
  }

  function __set($name, $value)
  {
    $this->_container[$name] = $value;
  }

  function __get($name)
  {
    if(isset($this->_container[$name]))
      return $this->_container[$name];
  }
}
?>