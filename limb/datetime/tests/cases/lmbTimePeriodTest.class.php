<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTimePeriodTest.class.php 4993 2007-02-08 15:35:44Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbTimePeriod.class.php');
lmb_require('limb/datetime/src/lmbDatePeriod.class.php');

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
    $this->assertEqual($date_period, new lmbDatePeriod('2006-12-01 13:45:12', '2006-12-01 13:46:00'));
  }
}

?>
