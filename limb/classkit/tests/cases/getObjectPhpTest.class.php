<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: getObjectPhpTest.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require('limb/classkit/src/util.inc.php');
lmb_require('limb/classkit/src/lmbObject.class.php');

class getObjectPhpTest extends UnitTestCase
{
  function testGetObjectPhpID()
  {
    $obj1 = new lmbObject();
    $obj2 = new lmbObject();

    preg_match('~^(\D+)(\d+)$~', lmb_php_object_id($obj1), $m1);
    preg_match('~^(\D+)(\d+)$~', lmb_php_object_id($obj2), $m2);

    $this->assertEqual($m1[1], $m2[1]);
    $this->assertEqual($m1[1], 'lmbObject');

    $this->assertTrue($m1[2] > 0);
    $this->assertTrue($m2[2] > 0);
    $this->assertTrue($m2[2] != $m1[2]);
  }

  function testGetObjectPhpIDException()
  {
    $str = 'wow';

    try
    {
      lmb_php_object_id($str);
      $this->assertTrue(false);
    }
    catch(lmbInvalidArgumentException $e){}
  }
}
?>