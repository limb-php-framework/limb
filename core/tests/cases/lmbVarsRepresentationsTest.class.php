<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2020 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

class lmbCommonFileTest extends UnitTestCase
{
  function testVarDump_SimpleTypes()
  {
  	$this->assertEqual('NULL', lmb_var_dump(null));
  	$this->assertEqual('TRUE', lmb_var_dump(true));
  	$this->assertEqual('INT(42)', lmb_var_dump(42));
  	$this->assertEqual('FLOAT(3.14159)', lmb_var_dump(3.14159));
  	$this->assertEqual("STRING(8) \"\tfoo\nbar\"", lmb_var_dump("\tfoo\nbar"));
  	$this->assertEqual('STRING(300) "'.str_repeat('a', 100).'..."', lmb_var_dump(str_repeat('a', 300)));

  	$resource = fopen(__FILE__, 'r');
  	$this->assertPattern('/RESOURCE\(#[0-9]*\) of type \(stream\)/', lmb_var_dump($resource));
  	fclose($resource);
  }

  function testVarDump_Objects()
  {
  	$child = new stdClass();
    $child->property = 42;
    $child->empty_object = new stdClass();
    $parent = new stdClass();
    $parent->child = $child;
    $parent->property = true;

    $expect =<<<EOD
OBJECT(stdClass) {
  ["child"]=> OBJECT(stdClass) {
    ["property"]=> INT(42)
    ["empty_object"]=> OBJECT(stdClass) {}
  }
  ["property"]=> TRUE
}
EOD;

    $this->assertEqual($expect, lmb_var_dump($parent));
  }

  function testVarDump_ObjectWithCircle()
  {
  	$child = new stdClass();
    $parent = new stdClass();

    $child->property = 42;
    $child->parent = $parent;
    $parent->child = $child;

    $expected =<<<EOD
OBJECT(stdClass) {
  ["child"]=> OBJECT(stdClass) {
    ["property"]=> INT(42)
    ["parent"]=> OBJECT(stdClass) {
      ["child"]=> OBJECT(stdClass) {
        ["property"]=> INT(42)
        ["parent"]=> OBJECT(stdClass) {
          ["child"]=> OBJECT(stdClass) {
            ["property"]=> INT(42)
            ["parent"]=> OBJECT(stdClass)
          }
        }
      }
    }
  }
}
EOD;

    $this->assertEqual($expected, lmb_var_dump($parent));
  }

  function testVarDump_Array()
  {
    $arr = array(42, true, array(3.14159), array());
    $expected =<<<EOD
ARRAY(4) [
  [0] => INT(42)
  [1] => TRUE
  [2] => ARRAY(1) [
    [0] => FLOAT(3.14159)
  ]
  [3] => ARRAY(0) []
]
EOD;
    $this->assertEqual($expected, lmb_var_dump($arr));
  }
}