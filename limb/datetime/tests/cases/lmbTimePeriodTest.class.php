<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/datetime/src/lmbTimePeriod.class.php');
lmb_require('limb/datetime/src/lmbDateTimePeriod.class.php');

class lmbTimePeriodTest extends UnitTestCase
{
  function testUseOnlyTime()
  {
    $p = new lmbTimePeriod('2005-12-01 13:45:12', '2006-12-01 13:46:00');
    $this->assertEqual($p->getDuration(), 48);
  }

  function testGetDatePeriod()
  {
    $p = new lmbTimePeriod('13:45:12', '13:46:00');
    $date_period = $p->getDatePeriod('2006-12-01');
    $this->assertEqual($date_period, new lmbDateTimePeriod('2006-12-01 13:45:12', '2006-12-01 13:46:00'));
  }
}


