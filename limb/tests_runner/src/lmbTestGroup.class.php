<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestGroup.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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