<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/tag_node/WactCompilerTag.class.php');
/**
 * Runtime component tags have a corresponding WactRuntimeComponent which represents
 * an API which can be used to manipulate the marked up portion of the template.
 * @package wact
 * @version $Id: WactRuntimeComponentTag.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactRuntimeComponentTag extends WactCompilerTag
{
  protected $runtimeIncludeFile = '';
  protected $runtimeComponentName = 'WactRuntimeComponent';

  protected $unique_id;
  protected $unique_var;

  function getComponentRefCode()
  {
    return '$components[\'' . $this->unique_id . '\']';
  }

  /**
  * Calls the parent getComponentRefCode() method and writes it to the
  * compiled template, appending an addChild() method used to create
  * this component at runtime
  */
  function generateConstructor($code_writer)
  {
    $this->generateUniqueId($code_writer);

    if ($this->runtimeIncludeFile)
      $code_writer->registerInclude($this->runtimeIncludeFile);

    $code_writer->writePHP($this->unique_var . ' = new ' . $this->runtimeComponentName .
      '(\'' . $this->getServerId() .'\');' . "\n");

    $code_writer->writePHP($this->getComponentRefCode() . ' = ' . $this->unique_var . ';' . "\n");

    $code_writer->writePHP($this->parent->getComponentRefCode() .
      '->addChild(' . $this->unique_var . ');' . "\n");

    parent :: generateConstructor($code_writer);
  }

  function generateUniqueId($code_writer)
  {
    $this->unique_id = $code_writer->getTempVariable();
    $this->unique_var = '$' . $this->unique_id;
  }
}

