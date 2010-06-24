<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface WactExpressionInterface.
 *
 * @package wact
 * @version $Id$
 */
interface WactExpressionInterface
{
  function isConstant();
  function getValue();
  function generatePreStatement($code_writer);
  function generateExpression($code_writer);
  function generatePostStatement($code_writer);
}

