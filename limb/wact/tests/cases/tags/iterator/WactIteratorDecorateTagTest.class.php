<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/WactArrayIteratorDecorator.class.php');

class TestingIteratorDecoratorTagDecorator extends WactArrayIteratorDecorator
{
  var $prefix = '!!!';

  function setPrefix($prefix)
  {
    $this->prefix = $prefix;
  }

  function current()
  {
    $record = parent :: current();
    $record['full'] = $this->prefix . $record['child'];
    return $record;
  }
}

class WactIteratorDecorateTagTest extends WactTemplateTestCase
{
  function testApplyDecorator()
  {
    $data = array (
      array ('name'=> 'joe', 'children' => array(array('child' => 'enny'),
                                                 array('child' => 'harry'))),
      array ('name'=> 'ivan', 'children' => array(array('child' => 'ann'),
                                                  array('child' => 'boris'))));

    $template = '<list:LIST id="fathers"><list:ITEM>{$name}:'.
                '<iterator:TRANSFER from="children" target="children">' .
                  '<iterator:decorate using="TestingIteratorDecoratorTagDecorator"/>'.
                '</iterator:TRANSFER>' .
                '<list:LIST id="children"><list:ITEM>{$full},</list:ITEM></list:LIST>'.
                '|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_decorator_simple.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_decorator_simple.html');

    $list = $page->getChild('fathers');
    $list->registerDataset($data);

    $this->assertEqual(trim($page->capture()), 'joe:!!!enny,!!!harry,|ivan:!!!ann,!!!boris,|');
  }

  function testApplyDecoratorWithParam()
  {
    $data = array (
      array ('name'=> 'joe', 'children' => array(array('child' => 'enny'),
                                                 array('child' => 'harry'))),
      array ('name'=> 'ivan', 'children' => array(array('child' => 'ann'),
                                                  array('child' => 'boris'))));

    $template = '<list:LIST id="fathers"><list:ITEM>{$name}:'.
                '<iterator:TRANSFER from="children" target="children">' .
                  '<iterator:decorate using="TestingIteratorDecoratorTagDecorator" prefix="+++"/>'.
                '</iterator:TRANSFER>' .
                '<list:LIST id="children"><list:ITEM>{$full},</list:ITEM></list:LIST>'.
                '|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/iterator/iterator_decorator_with_param.html', $template);

    $page = $this->initTemplate('/tags/iterator/iterator_decorator_with_param.html');

    $list = $page->getChild('fathers');
    $list->registerDataset($data);

    $this->assertEqual(trim($page->capture()), 'joe:+++enny,+++harry,|ivan:+++ann,+++boris,|');
  }
}

