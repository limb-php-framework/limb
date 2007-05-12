<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */
require_once('limb/wact/src/tags/fetch/WactBaseFetchingTag.class.php');

/**
* @tag iterator:TRANSFER
* @req_const_attributes to from
*/
class WactIteratorTransferTag extends WactBaseFetchingTag
{
  protected $runtimeComponentName = 'WactIteratorTransferComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/iterator/WactIteratorTransferComponent.class.php';

  function generateBeforeContent($code)
  {
    parent :: generateBeforeContent($code);

    $this->generateDereference($code);
  }

  function generateDereference($code_writer)
  {
    $from_dbe = new WactDataBindingExpressionNode($this->getAttribute('from'), $this);
    $from_dbe->generatePreStatement($code_writer);

    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataset(');

    $from_dbe->generateExpression($code_writer);

    $code_writer->writePHP(');');

    $from_dbe->generatePostStatement($code_writer);
  }
}

?>