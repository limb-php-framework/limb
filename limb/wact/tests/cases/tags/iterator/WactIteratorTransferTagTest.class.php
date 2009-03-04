<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class WactIteratorTransferTagTest extends WactTemplateTestCase
{
  function testTransfer()
  {
    $data = array (array ('name'=> 'joe', 'children' => array(array('child' => 'enny'),
                                                              array('child' => 'harry'))),
                      array ('name'=> 'ivan', 'children' => array(array('child' => 'ann'),
                                                                  array('child' => 'boris'))));

    $template = '<list:LIST id="fathers"><list:ITEM>{$name}:'.
                '<iterator:TRANSFER from="children" target="children" />' .
                '<list:LIST id="children"><list:ITEM>{$child},</list:ITEM></list:LIST>|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_transfer.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_transfer.html');

    $page->setChildDataSet('fathers', $data);

    $this->assertEqual($page->capture(), 'joe:enny,harry,|ivan:ann,boris,|');
  }

  function testOffsetTagAttribute()
  {
    $template =  '<iterator:transfer from="fathers" target="fathers" offset="1"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_transfer_with_offset.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_transfer_with_offset.html');

    $page->set('fathers', array(array('name'=> 'joe'),
                                array('name'=> 'ivan')));

    $this->assertEqual($page->capture(), 'ivan:');
  }

  function testLimitTagAttribute()
  {
    $template =  '<iterator:transfer from="fathers" target="fathers" limit="1"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_transfer_with_limit.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_transfer_with_limit.html');

    $page->set('fathers', array(array('name'=> 'joe'),
                                array('name'=> 'ivan')));

    $this->assertEqual($page->capture(), 'joe:');
  }

  function testUseComplexDBEWithFromAttribute()
  {
    $template =  '<iterator:transfer from="object.fathers" target="fathers"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_transfer_with_from_dbe.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_transfer_with_from_dbe.html');

    $data = array(array('name'=> 'joe'),
                  array('name'=> 'ivan'));

    $page->set('object', new ArrayObject(array('fathers' => $data)));

    $this->assertEqual($page->capture(), 'joe:ivan:');
  }

  function testCreatesEmptyArrayIteratorIfScalarlIsReceived()
  {
    $template =  '<iterator:transfer from="fathers" target="fathers"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_transfer_with_scalar_received.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_transfer_with_scalar_received.html');

    $page->set('fathers', 'whatever_scalar');

    $this->assertEqual($page->capture(), '');
  }
}

