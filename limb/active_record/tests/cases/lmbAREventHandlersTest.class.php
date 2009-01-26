<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbActiveRecordTest.class.php');

class lmbActiveRecordEventHaldlerStubDelegate
{
  var $calls_order = '';

  function onBeforeSave($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onBeforeSave ' . get_class($active_record) . '|';
  }

  function onAfterSave($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onAfterSave ' . get_class($active_record) . '|';
  }

  function onBeforeUpdate($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onBeforeUpdate ' . get_class($active_record) . '|';
  }

  function onUpdate($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onUpdate ' . get_class($active_record) . '|';
  }

  function onAfterUpdate($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onAfterUpdate ' . get_class($active_record) . '|';
  }

  function onBeforeCreate($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onBeforeCreate ' . get_class($active_record) . '|';
  }

  function onCreate($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onCreate ' . get_class($active_record) . '|';
  }

  function onAfterCreate($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onAfterCreate ' . get_class($active_record) . '|';
  }

  function onBeforeDestroy($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onBeforeDestroy ' . get_class($active_record) . '|';
  }

  function onAfterDestroy($active_record)
  {
    if($active_record instanceof lmbActiveRecord)
      $this->calls_order .= '|onAfterDestroy ' . get_class($active_record) . '|';
  }

  function getCallsOrder()
  {
    return $this->calls_order;
  }

  function subscribeForEvents($active_record)
  {
    $active_record->registerOnBeforeSaveCallback($this, 'onBeforeSave');
    $active_record->registerOnAfterSaveCallback($this, 'onAfterSave');
    $active_record->registerOnBeforeUpdateCallback($this, 'onBeforeUpdate');
    $active_record->registerOnUpdateCallback($this, 'onUpdate');
    $active_record->registerOnAfterUpdateCallback($this, 'onAfterUpdate');
    $active_record->registerOnBeforeCreateCallback($this, 'onBeforeCreate');
    $active_record->registerOnCreateCallback($this, 'onCreate');
    $active_record->registerOnAfterCreateCallback($this, 'onAfterCreate');
    $active_record->registerOnBeforeDestroyCallback($this, 'onBeforeDestroy');
    $active_record->registerOnAfterDestroyCallback($this, 'onAfterDestroy');
  }

  function subscribeGloballyForEvents()
  {
    lmbActiveRecord :: registerGlobalOnBeforeSaveCallback($this, 'onBeforeSave');
    lmbActiveRecord :: registerGlobalOnAfterSaveCallback($this, 'onAfterSave');
    lmbActiveRecord :: registerGlobalOnBeforeUpdateCallback($this, 'onBeforeUpdate');
    lmbActiveRecord :: registerGlobalOnUpdateCallback($this, 'onUpdate');
    lmbActiveRecord :: registerGlobalOnAfterUpdateCallback($this, 'onAfterUpdate');
    lmbActiveRecord :: registerGlobalOnBeforeCreateCallback($this, 'onBeforeCreate');
    lmbActiveRecord :: registerGlobalOnCreateCallback($this, 'onCreate');
    lmbActiveRecord :: registerGlobalOnAfterCreateCallback($this, 'onAfterCreate');
    lmbActiveRecord :: registerGlobalOnBeforeDestroyCallback($this, 'onBeforeDestroy');
    lmbActiveRecord :: registerGlobalOnAfterDestroyCallback($this, 'onAfterDestroy');
  }
}

class lmbAREventHandlersTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object');

  function testSaveNewRecord()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Super annotation');
    $object->set('content', 'Super content');
    $object->set('news_date', '2005-01-10');

    $delegate = new lmbActiveRecordEventHaldlerStubDelegate();
    $delegate->subscribeForEvents($object);

    $object->save();

    $this->assertEqual($delegate->getCallsOrder(),
                       '|onBeforeSave TestOneTableObject||onBeforeCreate TestOneTableObject||onCreate TestOneTableObject||onAfterCreate TestOneTableObject||onAfterSave TestOneTableObject|');
  }

  function testUpdateRecord()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Super annotation');
    $object->set('content', 'Super content');
    $object->set('news_date', '2005-01-10');
    $object->save();

    $delegate = new lmbActiveRecordEventHaldlerStubDelegate();
    $delegate->subscribeForEvents($object);

    $object->set('content', 'New Super content');
    $object->save();

    $this->assertEqual($delegate->getCallsOrder(),
                       '|onBeforeSave TestOneTableObject||onBeforeUpdate TestOneTableObject||onUpdate TestOneTableObject||onAfterUpdate TestOneTableObject||onAfterSave TestOneTableObject|');
  }

  function testDestroyRecord()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Super annotation');
    $object->set('content', 'Super content');
    $object->set('news_date', '2005-01-10');
    $object->save();

    $delegate = new lmbActiveRecordEventHaldlerStubDelegate();
    $delegate->subscribeForEvents($object);

    $object->destroy();

    $this->assertEqual($delegate->getCallsOrder(),
                       '|onBeforeDestroy TestOneTableObject||onAfterDestroy TestOneTableObject|');
  }

  function testSaveNewRecordForGlobalListener()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Super annotation');
    $object->set('content', 'Super content');
    $object->set('news_date', '2005-01-10');

    $delegate = new lmbActiveRecordEventHaldlerStubDelegate();
    $delegate->subscribeGloballyForEvents();

    $object->save();

    $this->assertEqual($delegate->getCallsOrder(),
                       '|onBeforeSave TestOneTableObject||onBeforeCreate TestOneTableObject||onCreate TestOneTableObject||onAfterCreate TestOneTableObject||onAfterSave TestOneTableObject|');
  }

  function testUpdateRecordForGlobalListener()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Super annotation');
    $object->set('content', 'Super content');
    $object->set('news_date', '2005-01-10');
    $object->save();

    $delegate = new lmbActiveRecordEventHaldlerStubDelegate();
    $delegate->subscribeGloballyForEvents($object);

    $object->set('content', 'New Super content');
    $object->save();

    $this->assertEqual($delegate->getCallsOrder(),
                       '|onBeforeSave TestOneTableObject||onBeforeUpdate TestOneTableObject||onUpdate TestOneTableObject||onAfterUpdate TestOneTableObject||onAfterSave TestOneTableObject|');
  }

  function testDestroyRecordForGlobalListener()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Super annotation');
    $object->set('content', 'Super content');
    $object->set('news_date', '2005-01-10');
    $object->save();

    $delegate = new lmbActiveRecordEventHaldlerStubDelegate();
    $delegate->subscribeGloballyForEvents($object);

    $object->destroy();

    $this->assertEqual($delegate->getCallsOrder(),
                       '|onBeforeDestroy TestOneTableObject||onAfterDestroy TestOneTableObject|');
  }
}

