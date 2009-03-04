<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactBaseFetchingTag.
 *
 * @package wact
 * @version $Id$
 */
class WactBaseFetchingTag extends WactRuntimeComponentTag
{
  function preParse()
  {
    if(!$this->hasAttribute('target') && !$this->hasAttribute('to'))
      $this->raiseCompilerError('Required attribute not found',
                                array('attribute' => 'target or to'));

    if($this->hasAttribute('target') && $this->hasAttribute('to'))
      $this->raiseCompilerError('Both target and buffer attribute are not supported');

    $this->_fillToAttributeFromTargetAttribute();

    return parent :: preParse();
  }

  function generateBeforeContent($code)
  {
    $this->_processTargetAttribute($code);

    if($this->getBoolAttribute('first') || $this->getBoolAttribute('one'))
      $code->writePhp($this->getComponentRefCode() . '->setOnlyFirstRecord();');

    if($order = $this->getAttribute('order'))
      $code->writePhp($this->getComponentRefCode() . '->setOrder("' . $order .'");');

    if($offset = $this->getAttribute('offset'))
      $code->writePhp($this->getComponentRefCode() . '->setOffset("' . $offset .'");');

    if($limit = $this->getAttribute('limit'))
      $code->writePhp($this->getComponentRefCode() . '->setLimit("' . $limit .'");');

    $navigator = $this->getAttribute('navigator');
    if(!empty($navigator))
    {
      $code->writePhp($this->getComponentRefCode() . '->setNavigator("' . $navigator .'");');
    }
  }

  function generateAfterContent($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->process();');
  }

  protected function _fillToAttributeFromTargetAttribute()
  {
    if(!$this->hasAttribute('target'))
      return;

    $pieces = explode(',', $this->getAttribute('target'));

    $to = array();
    foreach($pieces as $piece)
     $to[] = '[' . $piece . ']';

    $this->setAttribute('to', implode(',', $to));
  }

  protected function _processTargetAttribute($code_writer)
  {
    foreach($this->_getExpressionsFromTargetAttribute() as $expression)
    {
      $dbe = new WactDataBindingExpressionNode($expression, $this, $this->parent);
      $datasource = $dbe->getDatasourceContext();
      $field_name = $dbe->getFieldName();

      if($field_name && !$datasource->isDatasource())
        $this->raiseCompilerError('Wrong DBE datasource context in buffer attribute',
                                  array('expression' => $expression));

      if(count($dbe->getPathToTargetDatasource()))
      {
        $this->raiseCompilerError('Path based variable name is not supported in buffer attribute',
                                  array('expression' => $expression));
      }

      $dbe->generatePreStatement($code_writer);

      $code_writer->writePHP($this->getComponentRefCode() . '->addBuffer(');

      $code_writer->writePHP($datasource->getComponentRefCode());

      if($field_name)
        $code_writer->writePHP(', "' . $field_name . '"');

      $code_writer->writePHP(');' . "\n");

      $dbe->generatePostStatement($code_writer);
    }
  }

  protected function _getExpressionsFromTargetAttribute()
  {
    $result = array();

    $pieces = explode(',', $this->getAttribute('to'));

    foreach($pieces as $piece)
     $result[] = trim($piece);

    return $result;
  }
}


