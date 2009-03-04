<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Include another template into the current template
 * @tag core:INCLUDE
 * @req_const_attributes file
 * @forbid_end_tag
 * @package wact
 * @version $Id: include.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCoreIncludeTag extends WactCompilerTag
{
  protected $skip_vars = array('file', 'literal', 'source', 'in_datasource');
  protected $new_datasource_tag = null;
  /**
  * @param WactCompiler
  **/
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $locator = $compiler->getTemplateLocator();

    if(!$file = $this->getAttribute('file'))
      $this->raiseRequiredAttributeError($file);

    $source_file = $locator->locateSourceTemplate($file);
    if (empty($source_file))
      $this->raiseCompilerError('Template source file not found', array('file_name' => $file));

    if ($this->getBoolAttribute('literal'))
      $this->addChild(new WactTextNode(null, $locator->readTemplateFile($source_file)));
    elseif ($this->getBoolAttribute('source'))
    {
      $this->addChild(new WactTextNode(null, highlight_string($locator->readTemplateFile($source_file), true)));
    }
    else
    {
      if($this->getBoolAttribute('in_datasource'))
      {
        $this->_createNewDatasourceTag($compiler);
        $compiler->parseTemplate($file, $this->new_datasource_tag);
      }
      else
        $compiler->parseTemplate($file, $this);
    }
  }

  function generateBeforeContent($code_writer)
  {
    $ref = $this->_getProperDatasourceRefCode();

    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();
      if(in_array($name, $this->skip_vars))
        continue;

      $code_writer->writePHP($ref . '->set("' . $name . '", ');
      $this->attributeNodes[$key]->generateExpression($code_writer);
      $code_writer->writePHP(');');
    }
  }

  protected function _getProperDatasourceRefCode()
  {
    if($this->new_datasource_tag)
      return $this->new_datasource_tag->getComponentRefCode();
    else
      return $this->getDataSource()->getComponentRefCode();
  }

  protected function _createNewDatasourceTag($compiler)
  {
    $tag_dictionary = $compiler->getTagDictionary();
    $datasource_tag_info = $tag_dictionary->findTagInfo('core:datasource', array(), true, $this);
    $datasource_tag_info->load();
    $this->new_datasource_tag = new WactRuntimeDatasourceComponentTag($this->location_in_template,
                                                                      'core:datasource',
                                                                      $datasource_tag_info);
    $this->new_datasource_tag->setServerId($this->getServerId());
    $this->addChild($this->new_datasource_tag);
  }
}

