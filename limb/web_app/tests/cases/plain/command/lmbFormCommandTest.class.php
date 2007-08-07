<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/command/lmbFormCommand.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');

Mock :: generate('lmbValidator', 'MockValidator');
Mock :: generate('lmbErrorList', 'MockErrorList');

class lmbFormStubDelegate
{
  var $calls_order = '';
  var $halt_on_before = false;

  function haltOnBefore()
  {
    $this->halt_on_before = true;
  }

  function onBefore($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_before|';

    if($this->halt_on_before)
      $form->halt();
  }

  function onAfter($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_after|';
  }

  function onShow($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_show|';
  }

  function onBeforeValidate($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_before_validate|';
  }

  function onAfterValidate($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_after_validate|';
  }

  function onValid($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_valid|';
  }

  function onError($form)
  {
    if($form instanceof lmbFormCommand)
      $this->calls_order .= '|on_error|';
  }
}

class lmbFormStubChild extends lmbFormCommand
{
  var $calls_order = '';

  function _onBefore()
  {
    $this->calls_order .= '|on_before|';
  }

  function _onAfter()
  {
    $this->calls_order .= '|on_after|';
  }

  function _onShow()
  {
    $this->calls_order .= '|on_show|';
  }

  function _onBeforeValidate()
  {
    $this->calls_order .= '|on_before_validate|';
  }

  function _onAfterValidate()
  {
    $this->calls_order .= '|on_after_validate|';
  }

  function _onValid()
  {
    $this->calls_order .= '|on_valid|';
  }

  function _onError()
  {
    $this->calls_order .= '|on_error|';
  }
}

class lmbFormCommandTest extends UnitTestCase
{
  var $toolkit;
  var $request;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testSetViewFormDatasource()
  {
    $validator = new MockValidator();
    $command = new lmbFormCommand('some_template', 'form_id', $validator);
    $ds = new lmbSet('whatever');
    $command->setFormDatasource($ds);

    $this->assertReference($this->toolkit->getView()->getFormDatasource('form_id'), $ds);
  }

  function testCallbacksForNotSubmitted()
  {
    $command = new lmbFormCommand('some_template', 'form_id');
    $delegate = $this->_createFormStubDelegate($command);
    $command->perform();
    $this->assertEqual($delegate->calls_order, '|on_before||on_show||on_after|');
    $this->assertFalse($command->isSubmitted());
    $this->assertTrue($command->isValid());
  }

  function testOwnMethodsForNotSubmitted()
  {
    $command = new lmbFormStubChild('some_template', 'form_id');
    $command->perform();
    $this->assertEqual($command->calls_order, '|on_before||on_show||on_after|');
  }

  function testDelegateForSubmittedAndNotValid()
  {
    $validator = new MockValidator();
    $error_list = new MockErrorList();

    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('submitted' => 1)));

    $command = new lmbFormCommand('some_template', 'form_id', $validator);
    $command->setErrorList($error_list);

    $validator->expectOnce('validate', array($request));
    $validator->expectOnce('setErrorList', array($error_list));
    $error_list->setReturnValue('isValid', false);

    $delegate = $this->_createFormStubDelegate($command);
    $command->perform();

    $this->assertTrue($command->isSubmitted());
    $this->assertFalse($command->isValid());
    $this->assertEqual($delegate->calls_order, '|on_before||on_before_validate||on_error||on_after_validate||on_after|');
  }

  function testOwnMethodsForSubmittedAndNotValid()
  {
    $validator = new MockValidator();
    $error_list = new MockErrorList();

    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('submitted' => 1)));

    $command = new lmbFormStubChild('some_template', 'form_id', $validator);
    $command->setErrorList($error_list);

    $validator->expectOnce('validate', array($request));
    $validator->expectOnce('setErrorList', array($error_list));
    $error_list->setReturnValue('isValid', false);

    $command->perform();

    $this->assertEqual($command->calls_order, '|on_before||on_before_validate||on_error||on_after_validate||on_after|');
  }

  function testDelegateForSubmittedAndValid()
  {
    $validator = new MockValidator();
    $error_list = new MockErrorList();

    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('submitted' => 1)));

    $command = new lmbFormCommand('some_template', 'form_id', $validator);
    $command->setErrorList($error_list);

    $validator->expectOnce('validate', array($request));
    $validator->expectOnce('setErrorList', array($error_list));
    $error_list->setReturnValue('isValid', true);

    $delegate = $this->_createFormStubDelegate($command);

    $command->perform();

    $this->assertTrue($command->isSubmitted());
    $this->assertTrue($command->isValid());
    $this->assertEqual($delegate->calls_order, '|on_before||on_before_validate||on_valid||on_after_validate||on_after|');
  }

  function testOwnMethodsForSubmittedAndValid()
  {
    $validator = new MockValidator();
    $error_list = new MockErrorList();

    $this->toolkit->setRequest($request = new lmbHttpRequest(null, $get = null, $post = array('submitted' => 1)));

    $command = new lmbFormStubChild('some_template', 'form_id', $validator);
    $command->setErrorList($error_list);

    $validator->expectOnce('validate', array($request));
    $validator->expectOnce('setErrorList', array($error_list));
    $error_list->setReturnValue('isValid', true);

    $command->perform();

    $this->assertTrue($command->isSubmitted());
    $this->assertTrue($command->isValid());
    $this->assertEqual($command->calls_order, '|on_before||on_before_validate||on_valid||on_after_validate||on_after|');
  }

  function testHaltInvokeChain()
  {
    $command = new lmbFormCommand('some_template', 'form_id');

    $delegate = $this->_createFormStubDelegate($command);
    $request = lmbToolkit :: instance()->getRequest();
    $request->set('submitted', 1);

    $delegate->haltOnBefore();

    $command->perform();

    $this->assertEqual($delegate->calls_order, '|on_before|');
  }

  protected function _createFormStubDelegate($command)
  {
    $delegate = new lmbFormStubDelegate();
    $command->registerOnBeforeCallback($delegate, 'onBefore');
    $command->registerOnShowCallback($delegate, 'onShow');
    $command->registerOnAfterCallback($delegate, 'onAfter');
    $command->registerOnBeforeValidateCallback($delegate, 'onBeforeValidate');
    $command->registerOnAfterValidateCallback($delegate, 'onAfterValidate');
    $command->registerOnValidCallback($delegate, 'onValid');
    $command->registerOnErrorCallback($delegate, 'onError');
    return $delegate;
  }
}


