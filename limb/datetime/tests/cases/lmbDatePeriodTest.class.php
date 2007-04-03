<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDatePeriodTest.class.php 4993 2007-02-08 15:35:44Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbDatePeriod.class.php');

class lmbDatePeriodTest extends UnitTestCase
{
  function testInvalidPeriod()
  {
    try
    {
      $period = new lmbDatePeriod('2005-12-01 13:45:12', '2005-12-01 13:45:10');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testToString()
  {
    $p = new lmbDatePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
    $this->assertEqual($p->toString(), '2005-12-01 13:45:12 - 2005-12-01 13:46:00');
  }

  function testGetDuration()
  {
    $p = new lmbDatePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
    $this->assertEqual($p->getDuration(), 48);
  }

  function testIsEqual()
  {
    $p1 = new lmbDatePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
    $p2 = new lmbDatePeriod('2006-12-01 13:45:12', '2006-12-01 13:46:00');

    $this->assertTrue($p1->isEqual($p1));
    $this->assertFalse($p1->isEqual($p2));
  }

  function testIsInside()
  {
    $child = new lmbDatePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
    $parent = new lmbDatePeriod('2005-12-01 10:00:00', '2005-12-01 14:01:00');
    $intersect = new lmbDatePeriod('2005-12-01 11:00:00', '2005-12-01 15:01:00');

    $this->assertTrue($child->isInside($parent));
    $this->assertTrue($parent->includes($child));
    $this->assertFalse($parent->includes($intersect));
  }

  function testIntersects()
  {
    $period1 = new lmbDatePeriod("2005-09-05 14:40:00", "2005-09-05 14:41:50");
    $period2 = new lmbDatePeriod("2005-09-05 14:39:00", "2005-09-05 14:41:00");
    $period3 = new lmbDatePeriod("2006-09-05 14:39:00", "2006-09-05 14:41:00");

    $this->assertTrue($period1->intersects($period1));

    $this->assertTrue($period1->intersects($period2));
    $this->assertTrue($period2->intersects($period1));

    $this->assertFalse($period1->intersects($period3));
    $this->assertFalse($period3->intersects($period1));
  }
}

?>
