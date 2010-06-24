<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbProjectConstructor.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbConstructorUnitTestCase extends UnitTestCase
{
  protected $dir_for_test_case;
  /**
   * @var lmbDbConnection
   */
  protected $conn;

  function setUp()
  {
    parent::setUp();

    $this->conn = lmbToolkit::instance()->getDefaultDbConnection();

    $dir_for_tests = lmb_var_dir() . '/constructor/';

    $this->dir_for_test_case = $dir_for_tests . '/' . get_class($this);
    lmbFs::mkdir($this->dir_for_test_case);
  }

  function tearDown()
  {
    //lmbFs::rm(lmb_var_dir() . '/constructor/');
  }
}