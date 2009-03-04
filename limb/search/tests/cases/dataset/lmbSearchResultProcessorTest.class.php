<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/search/src/dataset/lmbSearchResultProcessor.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');

class lmbSearchResultProcessorTest extends UnitTestCase
{
  function testLeftAndRightGaps()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = '...tands for DataBase Managemen...';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setWords(array('database'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testGapsIntersect()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = 'DBMS stands for DataBase Management and manages';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setWords(array('database', 'and', 'stands'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testGapsIntersectAndTheresRightGap()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = 'DBMS stands for DataBase Managemen...';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setWords(array('database', 'stands'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testOnlyRightGap()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = 'DBMS stands for DataB...';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setWords(array('stands'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testOnlyLeftGap()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = '...ement and manages';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setWords(array('manages'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testGapsDontIntersect()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = 'DBMS stands for DataB...ement and manages';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setWords(array('stands','manages'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testMatchingLinesLimit()
  {
    $result = 'DBMS stands for DataBase Management and manages';
    $expected = 'DBMS stands for DataB...';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setMatchingLinesLimit(1);
    $processor->setWords(array('stands','manages'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testMatchesMarks()
  {
    $result = 'DBMS stands for DataBase Management and manages base';
    $expected = '...tands for <b>DataBase</b> Managemen...d manages <b>base</b>';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(10);
    $processor->setGapsPattern('...');
    $processor->setMatchLeftMark('<b>');
    $processor->setMatchRightMark('</b>');
    $processor->setWords(array('base','database'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }

  function testProcessorIsMultibyteAware()
  {
    $result = 'DBMS значит Система Управления Базами Данных';
    $expected = '...ачит <b>Система</b> Упра...';

    $processor = new lmbSearchResultProcessor(new lmbCollection(array(array('content' => $result))));
    $processor->setMatchedWordFoldingRadius(5);
    $processor->setGapsPattern('...');
    $processor->setMatchLeftMark('<b>');
    $processor->setMatchRightMark('</b>');
    $processor->setWords(array('Система'));

    $processor->rewind();
    $record = $processor->current();
    $this->assertEqual($expected, $record->get('content'));
  }
}


