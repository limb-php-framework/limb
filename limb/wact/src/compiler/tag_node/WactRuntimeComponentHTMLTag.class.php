<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactRuntimeComponentHTMLTag.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Server tag component tags are WactRuntimeComponentTags which also correspond to
* an HTML tag.
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
    $this->generateDynamicAttributeList($code_writer);
  }

  function preGenerate($code_writer)
  {
    parent::preGenerate($code_writer);

    $code_writer->writeHTML('<' . $this->getRenderedTag());

    $code_writer->writePHP($this->getComponentRefCode() . '->renderAttributes();');

    $this->generateExtraAttributes($code_writer);

    if ($this->emptyClosedTag)
      $code_writer->writeHTML(' /');

    $code_writer->writeHTML('>');
  }

  function postGenerate($code_writer)
  {
    if ($this->hasClosingTag)
      $code_writer->writeHTML('</' . $this->getRenderedTag() .  '>');

    parent::postGenerate($code_writer);
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
?>