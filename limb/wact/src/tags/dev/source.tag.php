<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Displays the source code written into the compiled template, for the
 * section of the template in contains.
 * Note that position of this tag <i>will</i> matter. It cannot be round a
 * an input tag in a form tag, for example, where the nesting level will
 * result in an error. It may also result in a mess in terms of HTML
 * @tag dev:SOURCE
 * @package wact
 * @version $Id: source.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactDevSourceTag extends WactCompilerTag {

  /**
   * Position in the WactCodeWriter::code string containing the compiled code,
   * at which the dev:source tag was inserted.
   * @var int
   */
  protected $startPos;

  /**
   * Writing mode the WactCodeWriter was in, when the dev:source tag was
   * inserted
   * @var int
   */
  protected $startMode;

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateBeforeContent($code_writer)
  {
    $this->startPos = strlen($code_writer->getCode());
    $this->startMode = $code_writer->getMode();
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateAfterContent($code_writer)
  {
    $source = substr($code_writer->getCode(),$this->startPos);

    if ( !$this->getBoolAttribute('raw') ) {

      // Could do this better,  perhaps with indents. Course that
      // needs some kind of parser... or use the Tokenizer.
      $tmp_source = str_replace('<?php',"\n<?php\n",$source);
      $tmp_source = str_replace(';',";\n",$tmp_source);
      $tmp_source = str_replace('{',"{\n",$tmp_source);
      $tmp_source = str_replace('}',"}\n",$tmp_source);
      $tmp_source = str_replace('?>',"\n?>\n",$tmp_source);

    } else {
      $tmp_source = $source;
    }

    $html_source = highlight_string($tmp_source, true);

    $html_source = '<div align="left"><hr /><h3>Source Dump:</h3>'
    .$html_source;
    $html_source .= '<hr /></div><div><h3>Component:</h3>';

    // Have to violate the API so highlighted source
    // is placed before real code, in case of PHP parse errors
    $code = substr($code_writer->getCode(),0,$this->startPos);
    if ( $this->startMode == WactCodeWriter::MODE_PHP ) {
      $code .= '?>';
      $code .= $html_source;
      $code .= '<?php ';
      $code .= $source;
    } else {
      $code .= $html_source;
      $code .= $source;
    }

    if ( $code_writer->getMode() == WactCodeWriter::MODE_PHP ) {
      $code .= '?><br /><hr /></div><?php ';
    } else {
      $code .=	'<br /><hr /></div>';
    }

    $code_writer->setCode($code);
  }
}

