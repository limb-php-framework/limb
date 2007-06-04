<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactRuntimeDatasourceComponentHTMLTag.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/src/compiler/tag_node/WactRuntimeComponentHTMLTag.class.php');

class WactRuntimeDatasourceComponentHTMLTag extends WactRuntimeComponentHTMLTag
{
  function generateBeforeContent($code_writer)
  {
    parent :: generateBeforeContent($code_writer);

    if($this->hasAttribute('from'))
    {
      $code_writer->writePHP($this->getComponentRefCode() . '->registerDataSource(');
      $this->attributeNodes['from']->generateExpression($code_writer);
      $code_writer->writePHP(');');
    }

    $id = $this->getServerId();
    $code_writer->writePHP('$' . $id . ' = ' . $this->getComponentRefCode() . "->getDataSource();\n");
  }

  function getDataSource()
  {
    return $this;
  }

  function isDataSource()
  {
    return TRUE;
  }
}
?>