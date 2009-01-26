<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

Mock::generate('lmbErrorList', 'MockErrorList');

abstract class lmbValidationRuleTestCase extends UnitTestCase
{
  protected $error_list;

  function setUp()
  {
    $this->error_list = new MockErrorList();
  }
}

