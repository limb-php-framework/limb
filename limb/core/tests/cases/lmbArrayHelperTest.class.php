<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbArrayHelper.class.php');

class lmbArrayHelperTest extends UnitTestCase
{
  function testArrayMerge()
  {
    $a = array('orange', 'nested' => array(1), 'b' => 1);
    $b = array('apple', 'nested' => array(2), 'a' => 1);

    $this->assertEqual(lmbArrayHelper :: arrayMerge($a, $b),
                       array('apple', 'nested' => array(2), 'b' => 1, 'a' => 1));
  }

  function testArrayMergeMany()
  {
    $a = array('orange', 'nested' => array(1), 'b' => 1);
    $b = array('apple', 'nested' => array(2), 'a' => 1);
    $c = array('banana', 'b' => 2);

    $this->assertEqual(lmbArrayHelper :: arrayMerge($a, $b, $c),
                       array('banana', 'nested' => array(2), 'b' => 2, 'a' => 1));

  }

  function testMap()
  {
    $map = array('foo' => 'foo1', 'bar' => 'bar1');
    $src = array('foo' => 1, 'bar' => 2);
    $dest = array();

    lmbArrayHelper :: Map($map, $src, $dest);

    $this->assertEqual($dest, array('foo1' => 1, 'bar1' => 2));
  }

  function testExplode()
  {
    $string = 'man:bob,dog:willy';
    $res = lmbArrayHelper :: explode(',', ':', $string);
    $this->assertEqual($res, array('man' => 'bob', 'dog' => 'willy'));
  }

  function testGetColumnValues()
  {
    $arr = array(array('foo' => 1), array('foo' => 2));

    $this->assertEqual(lmbArrayHelper :: getColumnValues('foo', $arr), array(1, 2));
  }

  function testGetMaxColumnValue()
  {
    $arr = array(array('foo' => 1), array('foo' => 2));

    $this->assertEqual(lmbArrayHelper :: getMaxColumnValue('foo', $arr, $pos), 2);
    $this->assertEqual($pos, 1);
  }

  function testGetMinColumnValue()
  {
    $arr = array(array('foo' => 1), array('foo' => 2));

    $this->assertEqual(lmbArrayHelper :: getMinColumnValue('foo', $arr, $pos), 1);
    $this->assertEqual($pos, 0);
  }

  function testToFlatArray()
  {
    $arr = array(1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4));

    lmbArrayHelper :: toFlatArray($arr, $result1);
    $this->assertEqual($result1, array(1, 'apple' => 2, 'basket[chips]' => 3, 'basket[nachoes]' => 4));

    lmbArrayHelper :: toFlatArray($arr, $result2, '_');
    $this->assertEqual($result2, array('_[0]' => 1, '_[apple]' => 2, '_[basket][chips]' => 3, '_[basket][nachoes]' => 4));
  }

  function testArrayMapRecursive()
  {
    $arr = array(1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4));

    $f = create_function('$v', 'return $v + 1;');
    lmbArrayHelper :: arrayMapRecursive($f, $arr);

    $this->assertEqual($arr, array(2, 'apple' => 3, 'basket' => array('chips' => 4, 'nachoes' => 5)));
  }

  function testSortArray()
  {
    $arr = array(array('a' => 1, 'b' => 2), array('a' => 2, 'b' => 1), array('a' => 2, 'b' => 0));

    $res = lmbArrayHelper :: sortArray($arr, array('a' => 'DESC', 'b' => 'ASC'));
    $this->assertEqual($res, array(2 => array('a' => 2, 'b' => 0), 1 => array('a' => 2, 'b' => 1), 0 => array('a' => 1, 'b' => 2)));

    $res = lmbArrayHelper :: sortArray($arr, array('a' => 'DESC', 'b' => 'ASC'), false);
    $this->assertEqual($res, array(array('a' => 2, 'b' => 0), array('a' => 2, 'b' => 1), array('a' => 1, 'b' => 2)));
  }
}


