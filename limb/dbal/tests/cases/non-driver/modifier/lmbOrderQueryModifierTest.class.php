<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/modifier/lmbOrderQueryModifier.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');

Mock :: generate('lmbSelectQuery', 'MockSelectQuery');

class lmbOrderQueryModifierTest extends UnitTestCase
{
  function testApplySingleOrderFieldDefaultOrder()
  {
    $query = new MockSelectQuery();

    $order = 'title';
    $modifier = new lmbOrderQueryModifier($order);

    $query->expectOnce('addOrder', array('title', 'ASC'));

    $modifier->applyTo($query);
  }

  function testApplySingleOrderFieldDefinedOrder()
  {
    $query = new MockSelectQuery();

    $order = 'title=DESC';
    $modifier = new lmbOrderQueryModifier($order);

    $query->expectOnce('addOrder', array('title', 'DESC'));

    $modifier->applyTo($query);
  }

  function testApplyTwoOrdersFieldWithDefinedOrder()
  {
    $query = new MockSelectQuery();

    $order = 'title=DESC,name=ASC';
    $modifier = new lmbOrderQueryModifier($order);

    $query->expectCallCount('addOrder', 2);
    $query->expectArgumentsAt(0, 'addOrder', array('title', 'DESC'));
    $query->expectArgumentsAt(1, 'addOrder', array('name', 'ASC'));

    $modifier->applyTo($query);
  }

  function testThrowExceptionInConstructorIfOrderIsNotDescOrAsc()
  {
    $query = new MockSelectQuery();

    $order = 'title=error';

    try
    {
      $modifier = new lmbOrderQueryModifier($order);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }
}
?>
