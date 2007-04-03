<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbComplexArrayTest.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */
lmb_require('limb/util/src/util/lmbComplexArray.class.php');

class lmbComplexArrayTest extends UnitTestCase
{
  function testArrayMerge()
  {
    $a = array('orange', 'nested' => array(1), 'b' => 1);
    $b = array('apple', 'nested' => array(2), 'a' => 1);

    $this->assertEqual(lmbComplexArray :: arrayMerge($a, $b),
                       array('apple', 'nested' => array(2), 'b' => 1, 'a' => 1));
  }

  function testMap()
  {
    $map = array('foo' => 'foo1', 'bar' => 'bar1');
    $src = array('foo' => 1, 'bar' => 2);
    $dest = array();

    lmbComplexArray :: Map($map, $src, $dest);

    $this->assertEqual($dest, array('foo1' => 1, 'bar1' => 2));
  }

  function testExplode()
  {
    $string = 'man:bob,dog:willy';
    $res = lmbComplexArray :: explode(',', ':', $string);
    $this->assertEqual($res, array('man' => 'bob', 'dog' => 'willy'));
  }

  function testGetColumnValues()
  {
    $arr = array(array('foo' => 1), array('foo' => 2));

    $this->assertEqual(lmbComplexArray :: getColumnValues('foo', $arr), array(1, 2));
  }

  function testGetMaxColumnValue()
  {
    $arr = array(array('foo' => 1), array('foo' => 2));

    $this->assertEqual(lmbComplexArray :: getMaxColumnValue('foo', $arr, $pos), 2);
    $this->assertEqual($pos, 1);
  }

  function testGetMinColumnValue()
  {
    $arr = array(array('foo' => 1), array('foo' => 2));

    $this->assertEqual(lmbComplexArray :: getMinColumnValue('foo', $arr, $pos), 1);
    $this->assertEqual($pos, 0);
  }

  function testToFlatArray()
  {
    $arr = array(1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4));

    lmbComplexArray :: toFlatArray($arr, $result1);
    $this->assertEqual($result1, array(1, 'apple' => 2, 'basket[chips]' => 3, 'basket[nachoes]' => 4));

    lmbComplexArray :: toFlatArray($arr, $result2, '_');
    $this->assertEqual($result2, array('_[0]' => 1, '_[apple]' => 2, '_[basket][chips]' => 3, '_[basket][nachoes]' => 4));
  }

  function testArrayMapRecursive()
  {
    $arr = array(1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4));

    $f = create_function('$v', 'return $v + 1;');
    lmbComplexArray :: arrayMapRecursive($f, $arr);

    $this->assertEqual($arr, array(2, 'apple' => 3, 'basket' => array('chips' => 4, 'nachoes' => 5)));
  }

  function testSortArray()
  {
    $arr = array(array('a' => 1, 'b' => 2), array('a' => 2, 'b' => 1), array('a' => 2, 'b' => 0));

    $res = lmbComplexArray :: sortArray($arr, array('a' => 'DESC', 'b' => 'ASC'));
    $this->assertEqual($res, array(2 => array('a' => 2, 'b' => 0), 1 => array('a' => 2, 'b' => 1), 0 => array('a' => 1, 'b' => 2)));

    $res = lmbComplexArray :: sortArray($arr, array('a' => 'DESC', 'b' => 'ASC'), false);
    $this->assertEqual($res, array(array('a' => 2, 'b' => 0), array('a' => 2, 'b' => 1), array('a' => 1, 'b' => 2)));
  }
}

?>