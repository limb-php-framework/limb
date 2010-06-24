<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Server tag component tags are WactRuntimeComponentTags which also correspond to
 * an HTML tag.
 * @package wact
 * @version $Id: WactRuntimeComponentHTMLTag.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactRuntimeComponentHTMLTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'WactRuntimeTagComponent';

  /**
  * Returns the XML tag name
  * @return string
  * @access protected
  */
  function getRenderedTag()
  {
    return $this->tag;
  }

  function generateExtraAttributes($code_writer)
  {
    $this->generateDynamicAttributeList($code_writer, $this->tag_info->getSuppressAttributes());
  }

  function generateBeforeContent($code_writer)
  {
    $this->generateBeforeOpenTag($code_writer);

    $this->_renderOpenTag($code_writer);

    $this->generateAfterOpenTag($code_writer);
  }

  function generateBeforeOpenTag($code_writer)
  {
  }

  function generateAfterOpenTag($code_writer)
  {
  }

  function generateAfterContent($code_writer)
  {
    $this->generateBeforeCloseTag($code_writer);

    $this->_renderCloseTag($code_writer);

    $this->generateAfterCloseTag($code_writer);
  }

  function generateBeforeCloseTag($code_writer)
  {
  }

  function generateAfterCloseTag($code_writer)
  {
  }

  protected function _renderOpenTag($code_writer)
  {
    $code_writer->writeHTML('<' . $this->getRenderedTag());

    $code_writer->writePHP($this->getComponentRefCode() . '->renderAttributes();');

    $this->generateExtraAttributes($code_writer);

    if ($this->emptyClosedTag)
      $code_writer->writeHTML(' /');

    $code_writer->writeHTML('>');
  }

  protected function _renderCloseTag($code_writer)
  {
    if ($this->hasClosingTag)
      $code_writer->writeHTML('</' . $this->getRenderedTag() .  '>');
  }

  /**
  * Writes the compiled template constructor from the runtime component,
  * assigning the attributes found at compile time to the runtime component
  * via a serialized string
  */
  function generateConstructor($code_writer)
  {
    parent :: generateConstructor($code_writer);

    // Determine which attributes should not propigate to runtime
    $CompileTimeAttributes = $this->tag_info->getSuppressAttributes();

    // Add the runat attribute to the list of attributes to filter out
    $CompileTimeAttributes[] = 'runat';
    $CompileTimeAttributes[] = 'wact:id';

    $code_writer->writePHP($this->getComponentRefCode() . '->setAttributes(unserialize(');
    $code_writer->writePHPLiteral(serialize($this->getAttributesAsArray($CompileTimeAttributes)));
    $code_writer->writePHP('));'."\n");
  }
}

