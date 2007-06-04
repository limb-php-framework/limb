<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

/**
 * @property Key
 * @tag_class WactListItemTag
 */
class WactListItemKeyProperty extends WactCompilerProperty
{
  function generateExpression($code_writer)
  {
    $ListList = $this->context->findParentByClass('WactListListTag');
    $code_writer->writePHP($ListList->getComponentRefCode() . '->key()');
  }

}
?>