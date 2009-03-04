<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/property/WactConstantProperty.class.php';

/**
 * Sets a property in the runtime DataSource, according the attributes of this
 * tag at compile time.
 * @tag core:SET
 * @forbid_end_tag
 * @package wact
 * @version $Id: set.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCoreSetTag extends WactCompilerTag
{
  function preParse()
  {
    if($this->hasAttribute('runtime') && !$this->getBoolAttribute('runtime'))
    {
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
  }

  function generateTagContent($code_writer)
  {
    $ref = $this->getDataSource()->getComponentRefCode();
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();
      $code_writer->writePHP($ref . '->set("' . $name . '", ');
      $this->attributeNodes[$key]->generateExpression($code_writer);
      $code_writer->writePHP(');');
    }
  }
}

