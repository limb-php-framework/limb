<?php
/**
 * class lmbMacroCopyTag.
 * @tag else
 * @forbid_end_tag
 */
class lmbMacroElseTag extends lmbMacroTag
{
  protected function _generateContent($code_writer)
  {
    if(!$this->findParentByClass('lmbMacroIfTag'))
    {
      throw new lmbMacroException('If tag not found');
    }
    $code_writer->writePHP('} else {');
  }
}
