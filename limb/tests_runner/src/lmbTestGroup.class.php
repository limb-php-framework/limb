<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbTestGroup.
 *
 * @package tests_runner
 * @version $Id: lmbTestGroup.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbTestGroup extends TestSuite
{
  protected $_fixture;
  protected $_container = array();

  function useFixture($fixture)
  {
    $this->_fixture = $fixture;
  }

  function run($reporter)
  {
    $this->_setUpFixture();

    $res = parent :: run($reporter);

    $this->_tearDownFixture();

    return $res;
  }

  protected function _setUpFixture()
  {
    if($this->_fixture)
      $this->_fixture->setUp();
  }

  protected function _tearDownFixture()
  {
    if($this->_fixture)
      $this->_fixture->tearDown();
  }
}
?>