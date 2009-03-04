<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property Key
 * @tag_class WactListItemTag
 * @package wact
 * @version $Id$
 */
class WactListItemKeyProperty extends WactCompilerProperty
{
  function generateExpression($code_writer)
  {
    $ListList = $this->context->findParentByClass('WactListListTag');
    $code_writer->writePHP($ListList->getComponentRefCode() . '->key()');
  }

}

