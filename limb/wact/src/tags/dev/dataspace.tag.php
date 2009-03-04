<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @version $Id: dataspace.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */

/**
 * Dumps a runtime dataspace for display using print_r or var_dump
 * @tag dev:DATASPACE
 * @forbid_end_tag
 * @package wact
 * @version $Id: dataspace.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactDevDataSpaceTag extends WactCompilerTag
{
  /**
   * @param WactCodeWriter
   */
  function generateChildren($code_writer)
  {
    parent::generateChildren($code_writer);
    if ( NULL !== ( $context = $this->getAttribute('context') ) ) {
      $contexts = array('root','parent','current');
      if ( !in_array($context,$contexts) ) {
        $context = 'current';
      }
    } else {
      $context = 'current';
    }
    if ( NULL !== ( $output = $this->getAttribute('output') ) ) {
      $outputs = array('print_r','var_dump');
      if ( !in_array($output,$outputs) ) {
        $output = 'print_r';
      }
    } else {
      $output = 'print_r';
    }
    $code_writer->writeHTML('<div aligh="left"><hr /><h3>Begin '.ucfirst($context).' DataSpace</h3><hr /></div>');
    switch ( $context ) {
      case 'root':
        $Context = $this->getRootDataSource();
        break;
      case 'parent':
        $Context = $this->getParentDataSource();
        break;
      default:
        $Context = $this->getDataSource();
        break;
    }
    $code_writer->writeHTML('<pre>');
    $code_writer->writePHP('if ( is_object('.$Context->getComponentRefCode().
      ') && method_exists ('.$Context->getComponentRefCode().',"export") ) {');
    $code_writer->writePHP($output.'('.$Context->getComponentRefCode().'->export());');
    $code_writer->writePHP('} else {');
    $code_writer->writeHTML('Dataspace unavailable');
    $code_writer->writePHP('}');
    $code_writer->writeHTML('</pre>');
    $code_writer->writeHTML('<div aligh="left"><hr /><h3>End '.ucfirst($context).' DataSpace</h3><hr /></div>');
  }
}

