<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: set.tag.php 5169 2007-02-28 17:29:36Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/property/WactConstantProperty.class.php';

/**
 * Sets a property in the runtime DataSource, according the attributes of this
 * tag at compile time.
 * @tag core:SET
 * @forbid_end_tag
 */
class WactCoreSetTag extends WactCompilerTag
{
  function preParse()
  {
    if($this->hasAttribute('runtime'))
      return;
    $DataSource = $this->getDataSource();
    foreach(array_keys($this->attributeNodes) as $key)
    {
      if(!$this->attributeNodes[$key]->isConstant())
        continue;

      $name = $this->attributeNodes[$key]->getName();
      $property = new WactConstantProperty($this->attributeNodes[$key]->getValue());
      $DataSource->registerProperty($name, $property);
    }
  }

  function generateContents($code_writer)
  {
    $ref = $this->getDataSource()->getComponentRefCode();
    foreach(array_keys($this->attributeNodes) as $key) {
      $name = $this->attributeNodes[$key]->getName();
      if($this->attributeNodes[$key]->isConstant()) {
        $code_writer->writePHP($ref . '->set("' . $name . '", ');
        $code_writer->writePHPLiteral($this->attributeNodes[$key]->getValue());
        $code_writer->writePHP(');');
      } else {
        $this->attributeNodes[$key]->generatePreStatement($code_writer);

        $code_writer->writePHP($ref . '->set("' . $name . '", ');
        $this->attributeNodes[$key]->generateExpression($code_writer);
        $code_writer->writePHP(');');

        $this->attributeNodes[$key]->generatePostStatement($code_writer);
      }
    }
  }
}
?>
