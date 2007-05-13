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
* @req_attributes to from
* @convert_to_expression from
*/
class WactIteratorTransferTag extends WactBaseFetchingTag
{
  protected $runtimeComponentName = 'WactIteratorTransferComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/iterator/WactIteratorTransferComponent.class.php';

  function generateBeforeContent($code)
  {
    parent :: generateBeforeContent($code);

    $code->writePHP($this->getComponentRefCode() . '->registerDataset(');
    $this->attributeNodes['from']->generateExpression($code);
    $code->writePHP(');');
  }
}

?>