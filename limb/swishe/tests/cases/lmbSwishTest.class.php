<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/swishe/src/lmbSwishHighlite.class.php');
lmb_require('limb/swishe/src/lmbSwishFocus.class.php');

class lmbSwishSearchTest extends UnitTestCase
{
  function testHighliteOne()
  {
    $query = 'foo';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'Boo yo-yo foo whatever');
    $this->assertEqual($result, 'Boo yo-yo *foo# whatever');
  }

  function testHighliteMultiple()
  {
    $query = 'foo bar';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'bar yo-yo foo whatever');
    $this->assertEqual($result, '*bar# yo-yo *foo# whatever');
  }

  function testHighliteIgnoreCase()
  {
    $query = 'FoO bAr';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'BAr yo-yo fOO whatever');
    $this->assertEqual($result, '*BAr# yo-yo *fOO# whatever');
  }

  function testHighliteNoOverlappingHappens()
  {
    $query = 'foo foobar';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'foo yo-yo foobar whatever');
    $this->assertEqual($result, '*foo# yo-yo *foobar# whatever');
  }

  function testHighliteNoOverlappingHappens2()
  {
    $query = 'foo foobar';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'foobar yo-yo foo whatever');
    $this->assertEqual($result, '*foobar# yo-yo *foo# whatever');
  }

  function testHighliteWithManySpaces()
  {
    $query = 'foo      foobar';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'foobar yo-yo foo whatever');
    $this->assertEqual($result, '*foobar# yo-yo *foo# whatever');
  }

  function testHighliteWithIgnoredWords()
  {
    $query = 'foo not  or  and  foobar';

    $high = new lmbSwishHighlite('*', '#');
    $result = $high->process($query, 'foobar yo-yo foo whatever');
    $this->assertEqual($result, '*foobar# yo-yo *foo# whatever');
  }

  function testFocusSimple()
  {
    $query = 'foo';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo foo whatever nope');
    $this->assertEqual($result, '... yo-yo foo whatever ...');
  }

  function testFocusSeveralWithLeftGapOnly()
  {
    $query = 'foo hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo wow yo foo whatever hey nope');
    $this->assertEqual($result, '... yo foo whatever hey nope');
  }

  function testFocusSeveralWithRightGapOnly()
  {
    $query = 'foo hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'yo foo whatever hey nope blah damn');
    $this->assertEqual($result, 'yo foo whatever hey nope ...');
  }

  function testFocusSeveralWithLeftAndRightGaps()
  {
    $query = 'foo hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo foo whatever hey nope yo-yo');
    $this->assertEqual($result, '... yo-yo foo whatever hey nope ...');
  }

  function testFocusSeveralWithAllGaps()
  {
    $query = 'foo hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo foo whatever dinky donk hey nope yo-yo');
    $this->assertEqual($result, '... yo-yo foo whatever ... donk hey nope ...');
  }

  function testFocusSeveralWithManySpaces()
  {
    $query = 'foo        hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo foo whatever dinky donk hey nope yo-yo');
    $this->assertEqual($result, '... yo-yo foo whatever ... donk hey nope ...');
  }

  function testFocusSeveralWithIgnoredWords()
  {
    $query = 'foo and   or   not  hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo foo whatever dinky donk hey nope yo-yo');
    $this->assertEqual($result, '... yo-yo foo whatever ... donk hey nope ...');
  }

  function testFocusIgnoreCase()
  {
    $query = 'fOo heY';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo Foo whatever dinky donk hEy nope yo-yo');
    $this->assertEqual($result, '... yo-yo Foo whatever ... donk hEy nope ...');
  }

  function testFocusBiggerRadius()
  {
    $query = 'romanov';

    $focus = new lmbSwishFocus(5, '...');
    $result = $focus->process($query, 'Boo yo-yo foo romanov nope');
    $this->assertEqual($result, 'Boo yo-yo foo romanov nope');
  }

  function testFocusNoMatch()
  {
    $query = 'hey';

    $focus = new lmbSwishFocus(5, '...');
    $result = $focus->process($query, 'Boo yo-yo foo romanov nope whatever');
    $this->assertEqual($result, 'Boo yo-yo foo romanov nope ...');
  }

  function testFocusExtraWhiteSpaceIgnored()
  {
    $query = 'hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'Boo yo-yo   foo     hey    nope whatever');
    $this->assertEqual($result, '... foo hey nope ...');
  }

  function testFocusInTheBeginning()
  {
    $query = 'hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'hey yo-yo foo');
    $this->assertEqual($result, 'hey yo-yo ...');
  }

  function testFocusInTheEnd()
  {
    $query = 'hey';

    $focus = new lmbSwishFocus(1, '...');
    $result = $focus->process($query, 'wow bar hey');
    $this->assertEqual($result, '... bar hey');
  }
}


