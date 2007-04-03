<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: output.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag core:OUTPUT
* @forbid_end_tag
*/
class WactCoreOutputTag extends WactCompilerTag
{
  protected $expression;

  function preParse($compiler)
  {
    $this->expression = new WactExpression($this->getAttribute('value'),
                                           $this,
                                           $compiler->getFilterDictionary(),
                                           'html');
  }

  function prepare()
  {
    $this->expression->prepare();
    parent::prepare();
  }

  function preGenerate($code_writer)
  {
    parent::preGenerate($code_writer);

    if ($this->expression->isConstant())
      $code_writer->writeHTML($this->expression->getValue());
    else
    {
      $this->expression->generatePreStatement($code_writer);
      $code_writer->writePHP('echo ');
      $this->expression->generateExpression($code_writer);
      $code_writer->writePHP(';');
      $this->expression->generatePostStatement($code_writer);
    }
  }
}
?>