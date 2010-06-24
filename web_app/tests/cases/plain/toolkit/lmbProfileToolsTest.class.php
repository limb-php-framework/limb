<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_app
 * @version $Id$
 */
lmb_require('limb/toolkit/src/lmbMockToolsWrapper.class.php');
lmb_require('limb/web_app/src/toolkit/lmbProfileTools.class.php');

class lmbProfileToolsTest extends UnitTestCase
{

  function setUp()
  {
    lmbToolkit :: save();
    lmbToolkit :: merge(new lmbProfileTools());
    $this->toolkit = lmbToolkit :: instance();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testProfileStartPoint()
  {
    $this->assertTrue($this->toolkit->hasProfileStartPoint());
    $this->toolkit->setProfileStartPoint($time = microtime(true));
    $this->assertEqual($this->toolkit->getProfilePoint('__start__'), $time);
  }

  function testProfileEndPoint()
  {
    $this->assertFalse($this->toolkit->hasProfileEndPoint());
    $this->toolkit->setProfileEndPoint();
    $this->assertTrue($this->toolkit->hasProfileEndPoint());
    $this->toolkit->setProfileEndPoint($time = microtime(true));
    $this->assertEqual($this->toolkit->getProfilePoint('__end__'), $time);
  }

  function testGetSetProfilePoint()
  {
    $this->toolkit->setProfilePoint('first');
    $this->assertNotNull($this->toolkit->getProfilePoint('first'));
    $this->toolkit->setProfilePoint('first', $time = microtime(true));
    $this->assertEqual($this->toolkit->getProfilePoint('first'), $time);
  }

  function testClearProfilePoint()
  {
    $this->toolkit->setProfilePoint('first', $time = microtime(true));
    $this->assertEqual($this->toolkit->getProfilePoint('first'), $time);
    $this->toolkit->clearProfilePoint('first');
    try
    {
      $this->toolkit->getProfilePoint('first');
      $this->fail("point first must be cleared!");
    }
    catch (Exception $e)
    {}
    try
    {
      $this->toolkit->clearProfilePoint('__start__');
      $this->fail("clearProfilePoint MUST NOT clear system points!");
    }
    catch (Exception $e)
    {}
    try
    {
      $this->toolkit->clearProfilePoint('__end__');
      $this->fail("clearProfilePoint MUST NOT clear system points!");
    }
    catch (Exception $e)
    {}
  }

  function testGetProfileTimeDiff()
  {
    $this->toolkit->setProfilePoint('first', $first = microtime(true));
    $this->toolkit->setProfilePoint('second', $second = $first + 100);
    $this->toolkit->setProfilePoint('third', $third = $second + 200);
    $this->assertEqual($this->toolkit->getProfileTimeDiff('__start__'), 0);
    $this->assertEqual($this->toolkit->getProfileTimeDiff('first'), $first - $this->toolkit->getProfilePoint('__start__'));
    $this->assertEqual($this->toolkit->getProfileTimeDiff('second'), $second - $first);
    // order of indexes doesn't matter
    $this->assertEqual($this->toolkit->getProfileTimeDiff('second', 'first'), $second - $first);
    $this->assertEqual($this->toolkit->getProfileTimeDiff('first', 'second'), $second - $first);
    $this->assertEqual($this->toolkit->getProfileTimeDiff('third', 'first'), $third - $first);
    try
    {
      $this->toolkit->getProfileTimeDiff('non-existing point');
      $this->fail('Non existing point must throw an exception');
    }
    catch (Exception $e)
    {}
  }

  function testGetProfileTotal()
  {
    $this->toolkit->setProfileStartPoint($start = microtime(true));
    $this->toolkit->setProfileEndPoint($end = $start + 200);
    $this->assertEqual($this->toolkit->getProfileTotal(), $end - $start);
  }

  function testGetProfileTotalSetsEndPoint()
  {
    $this->assertFalse($this->toolkit->hasProfileEndPoint());
    $this->toolkit->getProfileTotal();
    $this->assertTrue($this->toolkit->getProfilePoint('__start__') < $this->toolkit->getProfilePoint('__end__'));
  }

  function testGetProfilePercentDiff()
  {
    $this->toolkit->setProfileStartPoint($start = microtime(true));
    $this->toolkit->setProfilePoint('first', $first = $start + 500);
    $this->toolkit->setProfilePoint('second', $second = $first + 100);
    $this->toolkit->setProfilePoint('third', $third = $second + 200);
    $this->toolkit->setProfileEndPoint($end = $third + 300);
    $total = $end - $start;
    $this->assertEqual($this->toolkit->getProfilePercentDiff('first'), 100 * ($first - $start) / $total);
    $this->assertEqual($this->toolkit->getProfilePercentDiff('second'), 100 * ($second - $first) / $total);
    // order of indexes doesn't matter
    $this->assertEqual($this->toolkit->getProfilePercentDiff('second', 'first'), 100 * ($second - $first) / $total);
    $this->assertEqual($this->toolkit->getProfilePercentDiff('first', 'second'), 100 * ($second - $first) / $total);
    $this->assertEqual($this->toolkit->getProfilePercentDiff('third', 'first'), 100 * ($third - $first) / $total);
  }

  function testAddProfileDiffView()
  {
    $this->toolkit->setProfilePoint('first', $first = microtime(true));
    $this->toolkit->setProfilePoint('second', $second = $first + 100);
    $this->toolkit->setProfilePoint('third', $third = $second + 200);
    $this->assertEqual($this->toolkit->getProfileDiffViews(), array());
    $this->toolkit->addProfileDiffView('first', 'third');
    $this->toolkit->addProfileDiffView('first', '__end__', 'from first to end');
    $views = array(
      array(
        'first_point' => 'first',
        'second_point' => 'third'
      ),
      'from first to end' => array(
        'first_point' => 'first',
        'second_point' => '__end__'
      )
    );
    $this->assertEqual($this->toolkit->getProfileDiffViews(), $views);
  }

  function testGetProfileStat()
  {
    $this->toolkit->setProfilePoint('first', $first = microtime(true));
    usleep(10000);
    $this->toolkit->setProfilePoint('second', $second = microtime(true));
    $stat = "<pre>{$this->toolkit->showProfileStatItem('first')}
{$this->toolkit->showProfileStatItem('second')}

Total: {$this->toolkit->getProfileTotal()} sec.</pre>";
    $this->assertEqual($this->toolkit->getProfileStat(false), $stat);
    $this->toolkit->addProfileDiffView('first', '__end__');
    $this->toolkit->addProfileDiffView('__start__', 'second', 'From start to second');
    $stat = "<pre>{$this->toolkit->showProfileStatItem('first')}
{$this->toolkit->showProfileStatItem('second')}

Custom profile points:
{$this->toolkit->showProfileStatItem('first', '__end__')}
{$this->toolkit->showProfileStatItem('__start__', 'second', 'From start to second')}

Total: {$this->toolkit->getProfileTotal()} sec.</pre>";
    $this->assertEqual($this->toolkit->getProfileStat(false), $stat);
  }
}


