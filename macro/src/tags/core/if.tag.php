<?php
/**
 * class lmbMacroIfTag.
 * @tag if
 * @restrict_self_nesting
 */
class lmbMacroIfTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    if(!$this->has('var') && !$this->has('expr'))
      throw new lmbMacroException("'var'( alias 'expr') attribute is required for 'if' tag");
  }

  protected function _generateBeforeContent($code_writer)
  {
    $var = $this->has('var') ? $this->get('var') : $this->get('expr');
    $code_writer->writePHP('if('.$var.') {'.PHP_EOL);
  }

  protected function _generateAfterContent($code_writer)
  {
    $code_writer->writePHP('}'.PHP_EOL);
  }
}
