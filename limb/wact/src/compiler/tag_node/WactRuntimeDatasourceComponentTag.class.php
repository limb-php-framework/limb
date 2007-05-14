<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactRuntimeDatasourceComponentTag.class.php 5893 2007-05-14 15:05:54Z serega $
 * @package    wact
 */

require_once('limb/wact/src/compiler/tag_node/WactRuntimeComponentTag.class.php');

class WactRuntimeDatasourceComponentTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'WactDatasourceRuntimeComponent';

  function generateBeforeContent($code_writer)
  {
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