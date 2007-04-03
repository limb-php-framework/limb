<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompoundAttribute.class.php 5168 2007-02-28 16:05:08Z serega $
 * @package    wact
 */

require_once('limb/wact/src/compiler/attribute/WactAttributeNode.class.php');

/**
* Used to store complex expressions like "{$var1}_my_{$var2}" found inside tag attributes
*/
class WactCompoundAttribute implements WactExpressionInterface
{
  protected $name;
  protected $fragments = array();

  function __construct($name)
  {
    $this->name = $name;
  }

  function getName()
  {
    return $this->name;
  }

  function addAttributeFragment($fragment)
  {
    $this->fragments[] = $fragment;
  }

  function getFragment($index)
  {
    if(isset($this->fragments[$index]))
      return $this->fragments[$index];
  }

  function isConstant()
  {
    $isConstant = TRUE;
    foreach( array_keys($this->fragments) as $key)
      $isConstant = $isConstant && $this->fragments[$key]->isConstant();
    return $isConstant;
  }

  function getValue()
  {
    $value = "";
    foreach( array_keys($this->fragments) as $key)
      $value .= $this->fragments[$key]->getValue();

    return $value;
  }

  function generate($code_writer)
  {
    $code_writer->writeHTML(' ' . $this->name);
    $code_writer->writeHTML('="');

    foreach( array_keys($this->fragments) as $key)
        $this->fragments[$key]->generateFragment($code_writer);

    $code_writer->writeHTML('"');
  }

  function generatePreStatement($code_writer)
  {
    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('(');
    $separator = '';
    foreach( array_keys($this->fragments) as $key)
    {
      $code_writer->writePHP($separator);
      $this->fragments[$key]->generateExpression($code_writer);
      $separator = ".";
    }
    $code_writer->writePHP(')');
  }

  function generatePostStatement($code_writer)
  {
    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->generatePostStatement($code_writer);
  }

  function prepare()
  {
    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->prepare();
  }
}

?>