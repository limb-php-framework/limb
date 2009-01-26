<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

class lmbI18NDictionaryTest extends UnitTestCase
{
  function testIsEmpty()
  {
    $d = new lmbI18NDictionary();
    $this->assertTrue($d->isEmpty());
    $d->add('Hello', 'Привет');
    $this->assertFalse($d->isEmpty());
  }

  function testTranslateFailed()
  {
    $d = new lmbI18NDictionary();
    $this->assertFalse($d->has('Hello'));
    $this->assertEqual($d->translate('Hello'), 'Hello');
  }

  function testTranslateOk()
  {
    $d = new lmbI18NDictionary();
    $d->add('Hello', 'Привет');
    $this->assertTrue($d->has('Hello'));
    $this->assertEqual($d->translate('Hello'), 'Привет');
  }

  function testTranslateWithAttributes()
  {
    $d = new lmbI18NDictionary();
    $d->add('Hello {what}', 'Привет {what}');
    $this->assertEqual($d->translate('Hello {what}', array('{what}' => 'Bob')), 'Привет Bob');
  }

  function testSetTranslations()
  {
    $d = new lmbI18NDictionary();
    $d->setTranslations(array('Hello' => 'Привет'));
    $this->assertEqual($d->translate('Hello'), 'Привет');
  }

  function testMergeAppend()
  {
    $d1 = new lmbI18NDictionary();
    $d1->add('Hello', 'Привет');

    $d2 = new lmbI18NDictionary();
    $d2->add('Test', 'Тест');

    $d3 = $d1->merge($d2);

    $this->assertEqual($d3->translate('Hello'), 'Привет');
    $this->assertEqual($d3->translate('Test'), 'Тест');
  }

  function testMergeReplace()
  {
    $d1 = new lmbI18NDictionary();
    $d1->add('Hello', 'Привет');

    $d2 = new lmbI18NDictionary();
    $d2->add('Hello', 'Привет снова');

    $d3 = $d1->merge($d2);

    $this->assertEqual($d3->translate('Hello'), 'Привет снова');
  }

  function testIsTranslated()
  {
    $d = new lmbI18NDictionary();
    $d->add('Hello', 'Привет');
    $d->add('Test');

    $this->assertTrue($d->isTranslated('Hello'));
    $this->assertFalse($d->isTranslated('Test'));

    $this->assertEqual($d->translate('Test'), 'Test');
  }

  function testHasSameEntries()
  {
    $d1 = new lmbI18NDictionary();
    $d1->add('Hello', 'Привет');
    $d1->add('Test');

    $d2 = new lmbI18NDictionary();
    $d2->add('Test');
    $d2->add('Hello');

    $this->assertTrue($d1->hasSameEntries($d2));
    $this->assertFalse($d1->isEqual($d2));
  }

  function testHasNotSameEntries()
  {
    $d1 = new lmbI18NDictionary();
    $d1->add('Foo', 'Foo');
    $d1->add('Test');

    $d2 = new lmbI18NDictionary();
    $d2->add('Test');
    $d2->add('Bar');

    $this->assertFalse($d1->hasSameEntries($d2));
  }
}


