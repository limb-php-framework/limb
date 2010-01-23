<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactClassAnnotationParser.
 *
 * @package wact
 * @version $Id: WactClassAnnotationParser.class.php 8090 2010-01-23 05:55:20Z korchasa $
 */
class WactClassAnnotationParser
{
  var $listener;

  var $token;
  var $tokenStack = array();

  var $parentClassName;

  var $fsm;
  var $fsmPayLoad = array();

  var $allowedTokens = array (
      '{',
      '}',
      'T_COMMENT',
      'T_DOC_COMMENT',
      'T_CLASS',
      'T_EXTENDS',
      'T_FUNCTION',
      'T_STRING',
      'T_VAR',
      'T_VARIABLE',
  );

  function _reset()
  {
    $this->_ensureFSM();
    $this->token = null;
    $this->tokenStack = array();
    $this->fsmPayLoad = array();
    $this->fsm->reset();
  }

  function _ensureFSM()
  {
    if(!is_object($this->fsm))
      $this->fsm = $this->_createFSM();
  }

  function _createFSM()
  {
    require_once(dirname(__FILE__) . '/WactFSM.php');
    $fsm = new WactFSM('ST_INIT', $this->fsmPayLoad);

    // annotation
    $fsm->addTransition('T_COMMENT',    'ST_INIT',              'ST_INIT',            array($this, '_triggerAnnotations'));
    $fsm->addTransition('T_COMMENT',    'ST_IN_CLASS',          'ST_IN_CLASS',        array($this, '_triggerAnnotations'));
    $fsm->addTransition('T_DOC_COMMENT','ST_INIT',              'ST_INIT',            array($this, '_triggerAnnotations'));
    $fsm->addTransition('T_DOC_COMMENT','ST_IN_CLASS',          'ST_IN_CLASS',        array($this, '_triggerAnnotations'));

    // class begin
    $fsm->addTransition('T_CLASS',      'ST_INIT',              'ST_CLASS_AHEAD');
    $fsm->addTransition('T_STRING',     'ST_CLASS_AHEAD',       'ST_CLASS_NAME',      array($this, '_meetClassName'));
    $fsm->addTransition('{',            'ST_CLASS_NAME',        'ST_IN_CLASS',        array($this, '_triggerBeginClass'));

    $fsm->addTransition('T_EXTENDS',    'ST_CLASS_NAME',        'ST_CLASS_EXTENDS');
    $fsm->addTransition('T_STRING',     'ST_CLASS_EXTENDS',     'ST_CLASS_NAME',      array($this, '_meetParentClassName'));

    $fsm->addTransition('{',            'ST_IN_CLASS',          'ST_IN_CLASS',        array($this, '_openBlock'));

    // class end
    $fsm->addTransition('}',            'ST_IN_CLASS',          'ST_IN_CLASS',        array($this, '_closeBlock'));

    // property
    $fsm->addTransition('T_VAR',        'ST_IN_CLASS',          'ST_PROPERTY_AHEAD');
    $fsm->addTransition('T_VARIABLE',   'ST_PROPERTY_AHEAD',    'ST_IN_CLASS',        array($this, '_triggerProperty'));

    // method
    $fsm->addTransition('T_FUNCTION',   'ST_IN_CLASS',          'ST_METHOD_AHEAD');
    $fsm->addTransition('T_STRING',     'ST_METHOD_AHEAD',      'ST_IN_CLASS',        array($this, '_triggerMethod'));

    return $fsm;
  }

  function process($listener, $phpCode)
  {
    $this->_reset();

    $this->listener = $listener;

    $tokenNumber = 0;
    $tokens = token_get_all($phpCode);

    while ($tokenNumber < count($tokens))
    {
      $this->token = $tokens[$tokenNumber++];

      $token_name = is_array($this->token) ?  token_name($this->token[0]) : $this->token;
      if (in_array($token_name, $this->allowedTokens))
        $this->fsm->process($token_name);
    }
  }

  function _invokeListener($method, $params)
  {
    if(!is_array($params))
      $params = array($params);
    if (method_exists($this->listener, $method))
      call_user_func_array (array($this->listener, $method), $params);
  }

  function _triggerAnnotations()
  {
    if(preg_match_all('~@(\S+)([^\n]+)?\n~', $this->token[1], $matches))
    {
      for($i = 0; $i < count($matches[0]); $i++)
        $this->_invokeListener('annotation', array($matches[1][$i], trim($matches[2][$i])));
    }
  }

  function _meetClassName()
  {
      $this->className = $this->token[1];
      $this->parentClassName = null;
  }

  function _meetParentClassName()
  {
      $this->parentClassName = $this->token[1];
  }

  function _triggerBeginClass()
  {
      $this->_invokeListener('beginClass', array($this->className, $this->parentClassName));
      array_push($this->tokenStack, array('type' => 'endClass', 'params'=> array()));
  }

  function _openBlock()
  {
      array_push($this->tokenStack, null);
  }

  function _closeBlock()
  {
      if (($token = array_pop($this->tokenStack)) !== null) {
          $this->_invokeListener($token['type'], $token['params']);
          return 'ST_INIT';
      }
  }

  function _triggerProperty()
  {
      $this->_invokeListener('property', array(substr($this->token[1],1), 'public'));
  }

  function _triggerMethod()
  {
      $this->_invokeListener('method', $this->token[1]);
  }
}

