<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface WactBlockAnalizerListener.
 *
 * @package wact
 * @version $Id$
 */
interface WactBlockAnalizerListener
{
  function addLiteralFragment($text);
  function addExpressionFragment($text);
}

