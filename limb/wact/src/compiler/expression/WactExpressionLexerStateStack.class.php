<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionLexerStateStack.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 *    States for a stack machine.
 *    @package wact
  */
class WactExpressionLexerStateStack {
  var $_stack;

  /**
   *    Constructor. Starts in named state.
   *    @param string $start        Starting state name.
   *    @access public
   */
  function WactExpressionLexerStateStack($start) {
    $this->_stack = array($start);
  }

  /**
   *    Accessor for current state.
   *    @return string       State.
   *    @access public
   */
  function getCurrent() {
    return $this->_stack[count($this->_stack) - 1];
  }

  /**
   *    Adds a state to the stack and sets it
   *    to be the current state.
   *    @param string $state        New state.
   *    @access public
   */
  function enter($state) {
    array_push($this->_stack, $state);
  }

  /**
   *    Leaves the current state and reverts
   *    to the previous one.
   *    @return boolean    False if we drop off
   *                       the bottom of the list.
   *    @access public
   */
  function leave() {
    if (count($this->_stack) == 1) {
      return false;
    }
    array_pop($this->_stack);
    return true;
  }
}
?>