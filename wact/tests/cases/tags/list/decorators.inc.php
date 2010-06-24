<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class InnerDataSource extends IteratorIterator
{
  protected $number_source;
  protected $iterator;

  function __construct($number_source)
  {
    $this->number_source = $number_source;
  }

  function rewind()
  {
    $ns = $this->number_source->current();
    $Number = WactTemplate :: getValue($ns,'BaseNumber');

    if (!empty($Number))
    {
      $this->iterator = new WactArrayIterator(array(array('Num' => $Number),
                                                    array('Num' => $Number * $Number),
                                                    array('Num' => $Number * $Number * $Number)));
    }
    else
      $this->iterator = new WactArrayIterator(array());
    $this->iterator->rewind();
  }

  function current()
  {
    return $this->iterator->current();
  }

  function valid()
  {
    return $this->iterator->valid();
  }

  function next()
  {
    return $this->iterator->next();
  }
}

class NestedDataSetDecorator extends WactArrayIterator {

  function current()
  {
    $current = parent :: current();

    $current['sub'] = new WactArrayIterator(array(array('subvar1' => 'value1', 'subvar2' => 'value2'),
                                                  array('subvar1' => 'value3', 'subvar2' => 'value4'),
                                                  array('subvar1' => 'value5', 'subvar2' => 'value6')));
    return $current;
  }
}


